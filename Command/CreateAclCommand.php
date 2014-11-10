<?php

namespace Oneup\AclBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * CreateAclCommand
 *
 * @uses ContainerAwareCommand
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class CreateAclCommand extends ContainerAwareCommand
{
    /**
     * configure
     *
     * @access protected
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('oneup:acl:create')
            ->setDescription('Create an ACL for an Object')
            ->addArgument(
                'objectClass',
                InputArgument::REQUIRED,
                'Wich object type ?'
            )
            ->addArgument(
                'objectId',
                InputArgument::REQUIRED,
                'Wich object id ?'
            )
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Wich user ?'
            )
            ->addOption(
                'doctrine',
                'd',
                InputOption::VALUE_REQUIRED,
                'doctrine or doctrine_mongodb'
            )
            ->addOption(
                'entity-manager',
                'm',
                InputOption::VALUE_REQUIRED,
                'Entity manager'
            )
        ;
    }

    /**
     * execute
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @access protected
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // creating the ACL
        $objectClass = $input->getArgument('objectClass');
        $objectId = $input->getArgument('objectId');
        $doctrine = $input->getOption('doctrine');
        $entityManager = $input->getOption('entity-manager');

        if (!$doctrine) {
            $doctrine = 'doctrine';
        }

        if ($doctrine != 'doctrine' && $doctrine != 'doctrine_mongodb') {
            $output->writeln('<error>You have to choose between "doctrine" and "doctrine_mongodb"</error>');

            return 1;
        }

        $object = $this->get($doctrine)
            ->getManager($entityManager ?: null)
            ->getRepository($objectClass)
            ->find($objectId);

        if (!$object) {
            $output->writeln('<error>Unable to find the ' . $objectClass . ':' . $objectId . '</error>');

            return 1;
        }

        $aclProvider = $this->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
        }

        // retrieving the security identity of the currently logged-in user
        $username = $input->getArgument('username');
        $user = $this->get('fos_user.user_manager')
            ->findUserByUsernameOrEmail($username);

        if (!$user) {
            $output->writeln('<error>User ' . $username . ' not found.</error>');

            return 1;
        }

        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        // grant owner access

        $dialog = $this->getHelperSet()->get('dialog');
        $maskList = array('VIEW', 'EDIT', 'CREATE', 'DELETE', 'UNDELETE', 'OPERATOR', 'MASTER', 'OWNER');

        $maskInt = $dialog->select(
            $output,
            'Please select the ACL (default: VIEW)',
            $maskList,
            0
        );

        $mask = constant('Symfony\Component\Security\Acl\Permission\MaskBuilder::MASK_' . $maskList[$maskInt]);
        $acl->insertObjectAce($securityIdentity, $mask);
        $aclProvider->updateAcl($acl);

        $output->writeln('<info>ACL successfully updated.</info>');

        return 0;
    }

    /**
     * get
     *
     * @param  mixed $key
     * @access protected
     * @return mixed
     */
    protected function get($key)
    {
        return $this->getContainer()->get($key);
    }
}

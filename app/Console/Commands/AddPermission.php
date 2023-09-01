<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use  App\Models\Auth\UserRole;
use  App\Models\Auth\User;
use  App\Models\Auth\PermissionGroup;
use  App\Models\Auth\Permission;
use App\Models\Auth\Role;

class AddPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->addPermission(['name' => 'roles-permissions', 'label' => 'Allow users to add roles and their permissions', 'opt_group' => 'Users & Rights']);
        $this->addPermission(['name' => 'manage-users', 'label' => 'Allow one to add/update user', 'opt_group' => 'Users & Rights']);
        $this->addPermission(['name' => 'users', 'label' => 'Allow to see all users', 'opt_group' => 'Users & Rights']);
        $this->addPermission(['name' => 'modify-user-password', 'label' => 'Allow one to update user password', 'opt_group' => 'Users & Rights']);
        $this->addPermission(['name' => 'user-locations', 'label' => 'Allow to see user location', 'opt_group' => 'Users & Rights']);
        $this->addPermission(['name' => 'user-sites', 'label' => 'Allow to see user sites', 'opt_group' => 'Users & Rights']);
        $this->addPermission(['name' => 'user-active-deactive', 'label' => 'Allow to see user active & deactive', 'opt_group' => 'Users & Rights']);
        $this->addPermission(['name' => 'site-wise-user', 'label' => 'Allow to see site wise user', 'opt_group' => 'Users & Rights']);
        $this->addPermission(['name' => 'role-wise-permission', 'label' => 'Allow to see role wise permission', 'opt_group' => 'Users & Rights']);

        $this->addPermission(['name' => 'roles', 'label' => 'Allow to see all roles', 'opt_group' => 'Users & Rights']);
        $this->addPermission(['name' => 'role-modify', 'label' => 'Allow to add/update role', 'opt_group' => 'Users & Rights']);

        $this->addPermission(['name' => 'areas', 'label' => 'Allow to see all areas', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'areas-modify', 'label' => 'Allow to update area', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'departments', 'label' => 'Allow to see all departments', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'departments-modify', 'label' => 'Allow to update department', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'brands', 'label' => 'Allow to see all brands', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'brands-modify', 'label' => 'Allow to update brands', 'opt_group' => 'Masters']);


        $this->addPermission(['name' => 'machine-models', 'label' => 'Allow to see all machine models', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'machine-models-modify', 'label' => 'Allow to update machine models', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'machine-types', 'label' => 'Allow to see all machine types', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'machine-types-modify', 'label' => 'Allow to update machine type', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'problems', 'label' => 'Allow to see all problems', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'problems-modify', 'label' => 'Allow to update problem', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'maintenances', 'label' => 'Allow to see all maintenances', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'maintenances-modify', 'label' => 'Allow to update department', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'resolving-actions', 'label' => 'Allow to see all resolving-actions', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'resolving-actions-modify', 'label' => 'Allow to update resolving action', 'opt_group' => 'Masters']);



        $this->addPermission(['name' => 'machines', 'label' => 'Allow to see all machines', 'opt_group' => 'Machines']);
        $this->addPermission(['name' => 'machines-modify', 'label' => 'Allow to update machines record', 'opt_group' => 'Machines']);
        $this->addPermission(['name' => 'change-machines-location', 'label' => 'Allow to update machines location', 'opt_group' => 'Machines']);

        $this->addPermission(['name' => 'schedule-services', 'label' => 'Allow to see all scheduled services', 'opt_group' => 'Services']);
        $this->addPermission(['name' => 'schedule-services-modify', 'label' => 'Allow to update schedule services record', 'opt_group' => 'Services']);

        $this->addPermission(['name' => 'services', 'label' => 'Allow to see all services', 'opt_group' => 'Services']);
        $this->addPermission(['name' => 'services-modify', 'label' => 'Allow to update services record', 'opt_group' => 'Services']);

        $this->addPermission(['name' => 'cases-report', 'label' => 'Allow to see Cases report', 'opt_group' => 'Reports']);
        $this->addPermission(['name' => 'machines-report', 'label' => 'Allow to see machines report', 'opt_group' => 'Reports']);
        $this->addPermission(['name' => 'services-report', 'label' => 'Allow to see services report', 'opt_group' => 'Reports']);
        $this->addPermission(['name' => 'machines-summary-report', 'label' => 'Allow to see services machines summary report', 'opt_group' => 'Reports']);

        $this->addUser(['name' => 'Admin', 'email' => 'admin@cms.com', 'email_verified_at' => today(), 'password' => bcrypt(123456)]);
        echo 'Permissions updated!' . PHP_EOL;
    }



    private function addPermission($permission)
    {
        $opt_group_id = 0;
        if (isset($permission['opt_group'])) {
            $g = PermissionGroup::updateOrCreate(['opt_group' => $permission['opt_group']]);
            $opt_group_id = $g->id;
        }

        $p = Permission::updateOrCreate(['name' => $permission['name']], $permission + ['opt_group_id' => $opt_group_id]);
    }

    private function addUser($user_data)
    {
        $user = User::where('email', $user_data['email'])->first();
        if (!$user) {
            $user = User::create($user_data);
        }
        $role = Role::updateOrCreate(['name'=>'Admin']);
        $user_role =  UserRole::updateOrCreate(['user_id' => $user['id'], 'role_id' => $role['id']]);

    }


    private function addRole($role)
    {
        $user = Role::updateOrCreate(['name' => $role['name']], $role);
    }
}

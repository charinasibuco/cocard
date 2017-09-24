<?php

use Illuminate\Database\Seeder;

use Illuminate\Database\Eloquent\Model;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        DB::table('roles')->insert( [
            'title' => 'superadmin',
            'description' => 'manages activities within the workspace'
        ]);
        DB::table('roles')->insert( [
            'title' => 'administrator',
            'description' => 'manages activities within the workspace'
        ]);

        DB::table('roles')->insert([
            'title' => 'member',
            'description' => 'has access only/his or her account'
        ]);

       $permissions = [
                [
                   'title' => 'login_admin_dashboard',
                   'description' => "Able to login to admin dashboard",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_member_list',
                   'description' => "Able to view members page",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'add_member',
                   'description' => "Able to add a member",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'edit_member',
                   'description' => "Able to edit a member",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'delete_member',
                   'description' => "Able to delete a member",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_member',
                   'description' => "Able to view a member",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'assign_role',
                   'description' => 'Able to assign a role to a member',
                   'status'      => 'Active'
                ],
                [
                   'title' => 'assign_family',
                   'description' => 'Able to assign a member to a family',
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_event',
                   'description' => "Able to view events page",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'add_event',
                   'description' => "Able to add an event",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'edit_event',
                   'description' => "Able to edit an event",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'delete_event',
                   'description' => "Able to delete an event",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_donation',
                   'description' => "Able to view donations page",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_donation_category',
                   'description' => "Able to view donation categories",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'add_donation_category',
                   'description' => "Able to add donation category",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'edit_donation_category',
                   'description' => "Able to edit donation category",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'delete_donation_category',
                   'description' => "Able to delete donation category",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_donation_list',
                   'description' => "Able to view donation lists",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'add_donation_list',
                   'description' => "Able to add donation list",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'edit_donation_list',
                   'description' => "Able to edit donation list",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'delete_donation_list',
                   'description' => "Able to delete donation list",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_family',
                   'description' => "Able to view family page",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'add_family',
                   'description' => "Able to add a family",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'edit_family',
                   'description' => "Able to edit a family",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'delete_family',
                   'description' => "Able to delete a family",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_family_members',
                   'description' => "Able to view family members",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'add_user_family_member',
                   'description' => "Able to add family member",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'edit_user_family_member',
                   'description' => "Able to edit family member",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'delete_family_members',
                   'description' => "Able to delete family members",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'view_specific_family_member',
                   'description' => "Able to view specific family member",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'view_volunteer',
                   'description' => "Able to view volunteers page",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'view_staff',
                   'description' => "Able to view staffs page",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'add_staff',
                   'description' => "Able to add a staff",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'edit_staff',
                   'description' => "Able to edit a staff",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'delete_staff',
                   'description' => "Able to delete a staff",
                   'status'      => 'Active'
               ],
               [
                    'title' => 'view_roles',
                    'description' => 'Able to view role page',
                    'status'      => 'Active'
               ],
               [
                    'title' => 'add_role_permission',
                    'description' => 'Able to add a role and its permission',
                    'status'      => 'Active'
               ],
               [
                    'title' => 'update_role_permission',
                    'description' => 'Able to update a role and its permission',
                    'status'      => 'Active'
               ],
               [
                    'title' => 'delete_role_permission',
                    'description' => 'Able to delete a role and its permission',
                    'status'      => 'Active'
               ],
               [
                   'title' => 'view_email_group',
                   'description' => "Able to view email group page",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'add_email_group',
                   'description' => "Able to add an email group",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'edit_email_group',
                   'description' => "Able to edit an email group",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'delete_email_group',
                   'description' => "Able to delete an email group",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'message_email_group',
                   'description' => "Able to send message to email group members",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'view_email_member',
                   'description' => "Able to view email group members",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'add_email_member',
                   'description' => "Able to add an email group member",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'edit_email_member',
                   'description' => "Able to edit an email group member",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'delete_email_member',
                   'description' => "Able to delete an email group member",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'message_email_member',
                   'description' => "Able to send message to a specific member of an email group",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'backup_database',
                   'description' => "Able to backup database",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'generate_report',
                   'description' => "Able to generate report",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'view_admin_log',
                   'description' => "Able to view admin logs page",
                   'status'      => 'Active'
               ],
               [
                   'title' => 'edit_admin_settings',
                   'description' => "Able to edit admin settings",
                   'status'      => 'Active'
               ],
                [
                    'title' => 'view_users',
                    'description' => 'Able to view users',
                    'status'      => 'Active'
                ],
                [
                    'title' => 'add_user',
                    'description' => 'Able to add a user',
                    'status'      => 'Active'
                ],
                [
                    'title' => 'update_user',
                    'description' => 'Able to update a user',
                    'status'      => 'Active'
                ],
                [
                    'title' => 'update_account',
                    'description' => 'Able to update own account',
                    'status'      => 'Active'
                ],
                [
                    'title' => 'view_account',
                    'description' => 'Able to view own account',
                    'status'      => 'Active'
                ],
                [
                    'title' => 'delete_user',
                    'description' => 'Able to delete a user',
                    'status'      => 'Active'
                ],
                [
                    'title' => 'add_role',
                    'description' => 'Able to add a role',
                    'status'      => 'Active'
                ],
                [
                    'title' => 'update_role',
                    'description' => 'Able to update a role',
                    'status'      => 'Active'
                ],
                [
                    'title' => 'delete_role',
                    'description' => 'Able to delete a role',
                    'status'      => 'Active'
                ],
                [
                    'title' => 'view_permissions',
                    'description' => 'Able to view permission lists',
                    'status'      => 'Active'
                ],
                [
                   'title' => 'view_pages',
                   'description' => "Able to view page",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'add_page',
                   'description' => "Able to add page",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'edit_page',
                   'description' => "Able to update page",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'delete_page',
                   'description' => "Able to delete page",
                   'status'      => 'Active'
                ], 
                [
                   'title' => 'add_donation',
                   'description' => "Able to add a donation",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'edit_donation',
                   'description' => "Able to edit a donation",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'delete_donation',
                   'description' => "Able to delete a donation",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'add_family_members',
                   'description' => "Able to add family members",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'edit_family_members',
                   'description' => "Able to edit family members",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'delete_user_family_member',
                   'description' => "Able to delete family member",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'add_volunteer',
                   'description' => "Able to add a volunteer",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'edit_volunteer',
                   'description' => "Able to edit a volunteer",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'delete_volunteer',
                   'description' => "Able to delete a volunteer",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_superadmin_log',
                   'description' => "Able to view super admin log",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'hide_role',
                   'description' => "Hide restricted role",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'login_user_dashboard',
                   'description' => "Able to login to user dashboard",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'login_superadmin_dashboard',
                   'description' => "Able to login to superadmin dashboard",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'add_organization',
                   'description' => "Able to add an organization",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'edit_organization',
                   'description' => "Able to edit an organization",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'delete_organization',
                   'description' => "Able to delete an organization",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_organization',
                   'description' => "Able to view organizations",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'add_superadmin',
                   'description' => "Able to add superadmin",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'edit_superadmin',
                   'description' => "Able to edit superadmin",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'delete_superadmin',
                   'description' => "Able to delete superadmin",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_superadmin',
                   'description' => "Able to view superadmin",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_donation_history',
                   'description' => "Able to view and export donation history",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_event_history',
                   'description' => "Able to view and export event history",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_volunteer_history',
                   'description' => "Able to view and export volunteer history",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_own_family',
                   'description' => "Able to view own family and family members",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'can_donate',
                   'description' => "Able to donate",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'can_volunteer',
                   'description' => "Able to volunteer",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'join_event',
                   'description' => "Able to join an event",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_admin_org',
                   'description' => "Able to view admin list in an organization",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'add_admin_org',
                   'description' => "Able to add admin in an organization",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'edit_admin_org',
                   'description' => "Able to edit admin in an organization",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'delete_admin_org',
                   'description' => "Able to delete admin in an organization",
                   'status'      => 'Active'
                ],
                [
                   'title' => 'view_quickbooks',
                   'description' => "Able to view the quickbooks",
                   'status'      => 'Active'
                ],  
            ];

        DB::table('permissions')->insert($permissions);

        $role_permissions = [];

        /** roles for superadmin **/
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_pages')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_page')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_page')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_page')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_organization')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_organization')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_organization')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_organization')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_superadmin')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_superadmin')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_superadmin')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_superadmin')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','login_superadmin_dashboard')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_superadmin_log')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_users')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_user')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','update_user')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_user')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_role')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','update_role')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_role')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_roles')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_role_permission')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','update_role_permission')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_role_permission')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','assign_role')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_event')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_event')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_event')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_event')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_donation')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_donation')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_donation')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_donation')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_family')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_family')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_family')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_family')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_family_members')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_family_members')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_specific_family_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_user_family_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_user_family_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_user_family_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_volunteer')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_volunteer')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_volunteer')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_volunteer')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_staff')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_staff')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_staff')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_staff')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_email_group')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_email_group')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_email_group')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_email_group')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_email_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_email_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_email_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_email_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','message_email_group')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','message_email_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','backup_database')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','generate_report')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_admin_log')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_admin_settings')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','login_admin_dashboard')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_permissions')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_admin_org')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_admin_org')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_admin_org')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_admin_org')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_member_list')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_donation_category')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_donation_category')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_donation_category')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_donation_category')->first()->id
        ];
         $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_donation_list')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_donation_list')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_donation_list')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_donation_list')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','assign_family')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','superadmin')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_quickbooks')->first()->id
        ];
        

        

        /** roles for administrator **/
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','login_admin_dashboard')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_member_list')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','assign_role')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','assign_family')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_event')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_event')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_event')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_event')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_donation')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_donation_category')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_donation_category')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_donation_category')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_donation_category')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_donation_list')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_donation_list')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_donation_list')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_donation_list')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_family')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_family')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_family')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_family')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_family_members')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_user_family_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_user_family_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_family_members')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_specific_family_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_volunteer')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_staff')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_staff')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_staff')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_staff')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_roles')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_role_permission')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','update_role_permission')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_role_permission')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_email_group')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_email_group')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_email_group')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_email_group')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','message_email_group')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_email_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','add_email_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_email_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','delete_email_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','message_email_member')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','backup_database')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','generate_report')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_admin_log')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','edit_admin_settings')->first()->id
        ];
        $role_permissions[] =  [
                'role_id' => DB::table('roles')->where('title','administrator')->first()->id,
                'permission_id' => DB::table('permissions')->where('title','view_quickbooks')->first()->id
        ];



        /** roles for member **/
        $role_permissions[] =  [
            'role_id' => DB::table('roles')->where('title','member')->first()->id,
            'permission_id' => DB::table('permissions')->where('title','view_account')->first()->id
        ];
        $role_permissions[] =  [
            'role_id' => DB::table('roles')->where('title','member')->first()->id,
            'permission_id' => DB::table('permissions')->where('title','update_account')->first()->id
        ];
        $role_permissions[] =  [
            'role_id' => DB::table('roles')->where('title','member')->first()->id,
            'permission_id' => DB::table('permissions')->where('title','add_family_members')->first()->id
        ];
        $role_permissions[] =  [
            'role_id' => DB::table('roles')->where('title','member')->first()->id,
            'permission_id' => DB::table('permissions')->where('title','edit_family_members')->first()->id
        ];
        $role_permissions[] =  [
            'role_id' => DB::table('roles')->where('title','member')->first()->id,
            'permission_id' => DB::table('permissions')->where('title','delete_family_members')->first()->id
        ];
        $role_permissions[] =  [
            'role_id' => DB::table('roles')->where('title','member')->first()->id,
            'permission_id' => DB::table('permissions')->where('title','view_specific_family_member')->first()->id
        ];
        $role_permissions[] =  [
            'role_id' => DB::table('roles')->where('title','member')->first()->id,
            'permission_id' => DB::table('permissions')->where('title','view_own_family')->first()->id
        ];
        $role_permissions[] =  [
            'role_id' => DB::table('roles')->where('title','member')->first()->id,
            'permission_id' => DB::table('permissions')->where('title','view_donation_history')->first()->id
        ];
        $role_permissions[] =  [
            'role_id' => DB::table('roles')->where('title','member')->first()->id,
            'permission_id' => DB::table('permissions')->where('title','view_volunteer_history')->first()->id
        ];
        $role_permissions[] =  [
            'role_id' => DB::table('roles')->where('title','member')->first()->id,
            'permission_id' => DB::table('permissions')->where('title','view_event_history')->first()->id
        ];
        $role_permissions[] =  [
            'role_id' => DB::table('roles')->where('title','member')->first()->id,
            'permission_id' => DB::table('permissions')->where('title','can_donate')->first()->id
        ];
        $role_permissions[] =  [
            'role_id' => DB::table('roles')->where('title','member')->first()->id,
            'permission_id' => DB::table('permissions')->where('title','can_volunteer')->first()->id
        ];
        $role_permissions[] =  [
            'role_id' => DB::table('roles')->where('title','member')->first()->id,
            'permission_id' => DB::table('permissions')->where('title','join_event')->first()->id
        ];
        $role_permissions[] =  [
            'role_id' => DB::table('roles')->where('title','member')->first()->id,
            'permission_id' => DB::table('permissions')->where('title','login_user_dashboard')->first()->id
        ];

        DB::table('role_permissions')->insert($role_permissions);

        $assigned_user_roles = [
          ['role_id' => '1', 'user_id' => '1'],
          ['role_id' => '2', 'user_id' => '2'],
          ['role_id' => '2', 'user_id' => '3'],
          ['role_id' => '2', 'user_id' => '4'],
          ['role_id' => '3', 'user_id' => '5'],
          ['role_id' => '3', 'user_id' => '6'],
          ['role_id' => '3', 'user_id' => '7'],
        ];

        DB::table('assigned_user_roles')->insert( $assigned_user_roles);

        $user_roles = [
        	['role_id' => '1', 'user_id' => '1', 'original_user_id' => '1'],
        	['role_id' => '2', 'user_id' => '2', 'original_user_id' => '2'],
          ['role_id' => '2', 'user_id' => '3', 'original_user_id' => '3'],
          ['role_id' => '2', 'user_id' => '4', 'original_user_id' => '4'],
          ['role_id' => '3', 'user_id' => '5', 'original_user_id' => '5'],
          ['role_id' => '3', 'user_id' => '6', 'original_user_id' => '6'],
          ['role_id' => '3', 'user_id' => '7', 'original_user_id' => '7'],
        ];

        DB::table('user_roles')->insert( $user_roles);

        $org_roles = [
          ['role_id' => '1', 'organization_id' => '0'],
          ['role_id' => '2', 'organization_id' => '0'],
          ['role_id' => '3', 'organization_id' => '0']
        ];

        DB::table('org_roles')->insert( $org_roles);
    }
}

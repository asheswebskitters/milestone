<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin panel menu items
    |--------------------------------------------------------------------------
    |
    | Here you can edit the items to show in the admin menu(on top of the page)
    |
    */
    "list" => [
            [
                    "name"        => "Dashboard",
                    "route"       => "dashboard.default",
                    "link"        => '/admin/users/dashboard',
                    "icon"        => 'fa-dashboard',  
                    "permissions" => [],
                    "subpages" => []
            ],
            [
                /*
                 * the name of the link: you will see it in the admin menu panel.
                 * Note: If you don't want to show this item in the menu
                 * but still want to handle permission with the 'can_see' filter
                 * just leave this field empty.
                 */
                "name"        => "Users",
                /* the route name associated to the link: used to set
                 * the 'active' flag and to validate permissions of all
                 * the subroutes associated(users.* will be validated for _superadmin and _group-editor permission)
                 */
                "route"       => "users.list",
                /*
                 * the actual link associated to the menu item
                 */
                "link"        => '/admin/users/list',
                "icon"        => 'fa-user',
                /*
                 * the list of 'permission name' associated to the menu
                 * item: if the logged user has one or more of the permission
                 * in the list he can see the menu link and access the area
                 * associated with that.
                 * Every route that you create with the 'route' as a prefix
                 * will check for the permissions and throw a 401 error if the
                 * check fails (for example in this case every route named users.*)
                 */
                "permissions" => ["_superadmin", "_user-editor"],
                "subpages" => [],
                /*
                 * if there is any route that you want to skip for the permission check
                 * put it in this array
                 */
                "skip_permissions" => ["users.selfprofile.edit", "users.profile.edit", "users.profile.addfield", "users.profile.deletefield"]
            ],
            [
                    "name"        => "Permissions",
                    "route"       => "groups.list",
                    "link"        => '/admin/roles',
                    "icon"        => 'fa-filter',
                    "permissions" => ["_superadmin", "_group-editor"],
                    "subpages" => []
            ],
          /*[
                    "name"        => "Permission",
                    "route"       => "permission.list",
                    "link"        => '/admin/permissions/list',
                    "permissions" => ["_superadmin", "_permission-editor"],
                    "subpages" => []
            ],*/
            [
                /*
                 * Route to edit the current user profile
                 */
                "name"        => "",
                "route"       => "selfprofile",
                "link"        => '/admin/users/profile/self',
                "icon"        => 'fa-users',
                "permissions" => [],
                "subpages" => []
            ],

            [
                /*
                 * Menu to pages listing
                 */
                "name"        => "Pages",
                "route"       => "pages.list",
                "link"        => '/admin/pages/list',
                "icon"        => 'fa-file',
                "permissions" => [],
                "subpages" => []
            ],

            [
                /*
                 * Menu to pages listing
                 */
                "name"        => "Slides",
                "route"       => "slides.index",
                "link"        => '/admin/slides/',
                "icon"        => 'fa-file',
                "permissions" => [],
                "subpages" => []
            ],
            [
                /*
                 * Menu to Plans listing
                 */
                "name"        => "Plans",
                "route"       => "plans.index",
                "link"        => '/admin/plans/',
                "icon"        => 'fa-money',
                "permissions" => [],
                "subpages" => [
								
							],
            ],
             [
                /*
                 * Menu to Currency Exchange listing
                 */
                "name"        => "Currency",
                "route"       => "currency.index",
                "link"        => '/admin/currency/',
                "icon"        => 'fa-dollar',
                "permissions" => [],
                "subpages" => []
            ],
            [
                /*
                 * Menu to School listing
                 */
                "name"        => "Schools",
                "route"       => "schools.list",
                "link"        => '/admin/schools/list',
                "icon"        => 'fa-child',
                "permissions" => [],
                "subpages" => []
            ],
         /*   [                "name"        => "Age Categories",
                "route"       => "category.index",
                "link"        => '/admin/category/',
                "icon"        => 'fa-th-list',
                "permissions" => [],
                "subpages" => []
            ],*/

            [
                /*
                 * Menu to Coupon listing
                 */
                "name"        => "Coupons",
                "route"       => "coupons.index",
                "link"        => '/admin/coupons/',
                "icon"        => 'fa-gift',
                "permissions" => [],
                "subpages" => []
            ],
            [
                /*
                 * Menu to Coupon listing
                 */
                "name"        => "Subscriptions",
                "route"       => "subscription.list",
                "link"        => '/admin/subscription/list',
                "icon"        => 'fa-money',
                "permissions" => [],
                "subpages" => []
            ],
            [
                /*
                 * Menu to Coupon listing
                 */
                "name"        => "Settings",
                "route"       => "setting.edit",
                "link"        => '/admin/setting/',
                "icon"        => 'fa-gears',
                "permissions" => [],
                "subpages" => []
            ],

    ]
];
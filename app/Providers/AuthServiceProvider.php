<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();




        Gate::define('MTPR', function ($user) {
            return $user->rule === 'superuser' || $user->rule === 'MTPR';
        });

        Gate::define('Internal Teknologi', function ($user) {
            return $user->rule === 'superuser' ||
                $user->rule === 'Senior Manager Desain' ||
                $user->rule === 'Senior Manager Teknologi Produksi' ||
                $user->rule === 'Senior Manager Engineering' ||
                $user->rule === 'MTPR' || $user->rule === 'Manager MTPR' ||
                $user->rule === "Product Engineering" || $user->rule === 'Manager Product Engineering' ||
                $user->rule === 'Electrical Engineering System' || $user->rule === 'Manager Electrical Engineering System' ||
                $user->rule === 'Mechanical Engineering System' || $user->rule === 'Manager Mechanical Engineering System' ||
                $user->rule === 'Quality Engineering' || $user->rule === 'Manager Quality Engineering' ||
                $user->rule === 'RAMS' || $user->rule === 'Manager RAMS' ||
                $user->rule === 'Desain Mekanik & Interior' || $user->rule === 'Manager Desain Mekanik & Interior' ||
                $user->rule === 'Desain Bogie & Wagon' || $user->rule === 'Manager Desain Bogie & Wagon' ||
                $user->rule === 'Desain Carbody' || $user->rule === 'Manager Desain Carbody' ||
                $user->rule === 'Desain Elektrik' || $user->rule === 'Manager Desain Elektrik' ||
                $user->rule === 'Preparation & Support' || $user->rule === 'Manager Preparation & Support' ||
                $user->rule === 'Welding Technology' || $user->rule === 'Manager Welding Technology' ||
                $user->rule === 'Shop Drawing' || $user->rule === 'Manager Shop Drawing' ||
                $user->rule === 'Teknologi Proses' || $user->rule === 'Manager Teknologi Proses';
        });




        // New Gates for Approval
        Gate::define('Approval', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('Memo Sekdiv', function ($user) {
            $allowedRoles = [
                'superuser',

                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('Memo', function ($user) {
            $allowedRoles = [
                'superuser',
                'Logistik',
                'Manager Logistik',
                'Senior Manager Logistik',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ],
                [
                    'QC FAB',
                    'QC FIN',
                    'QC FAB',
                    'QC FIN',
                    'QC INC',
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('KOMREV', function ($user) {
            $allowedRoles = [
                'superuser',
                'Logistik',
                'Manager Logistik',
                'Senior Manager Logistik',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        // New Gates for Newbom
        Gate::define('NewbomkOMAT', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Logistik',
                'Manager Logistik',
                'Senior Manager Logistik',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering',
                'Logistik'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        // New Gates for Progress
        Gate::define('Progress', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ],
                [
                    'QC FAB',
                    'QC FIN',
                    'QC FAB',
                    'QC FIN',
                    'QC INC',
                    'QC Banyuwangi',
                    'Fabrikasi',
                    'PPC',
                    'Pabrik Banyuwangi',
                    'Fabrikasi',
                    'PPC',
                    'QC Banyuwangi',
                    'Finishing Bogie',
                    'Fabrikasi Bogie',
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        // New Gates for unit EIM
        Gate::define('Technology Management', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('Proker & LPK', function ($user) {
            $allowedRoles = [
                'superuser',

                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('Library', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ],
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('AI Custom', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('Inventaris', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('Library', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        // New Gates for Jobticket
        Gate::define('Jobticket', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];
            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );



            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        // New Gates for Katalog Komat
        Gate::define('KatalogKomat', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ],
                [
                    'Finishing Bogie',
                    'Fabrikasi Bogie',
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        // New Gates for Meeting
        Gate::define('Rapat', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        // New Gates for unit Product Engineering
        Gate::define('Product Engineering', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('Notulen', function ($user) {
            $allowedRoles = [
                'superuser',

                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('Rolling Stock', function ($user) {
            $allowedRoles = [
                'superuser',

                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('TACK', function ($user) {
            $allowedRoles = [
                'superuser',

                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        // New Gates for unit RAMS
        Gate::define('RAMS', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('Ramsdocument', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('FTA', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [],

            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('FMECA', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [],

            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('RBD', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [],

            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('PBS', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [],

            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });
        Gate::define('Hazardlog', function ($user) {
            return $user->rule === 'superuser' || $user->rule === 'MTPR';
        });

        // New Gates for unit RAMS
        Gate::define('innovation', function ($user) {
            $allowedRoles = [
                'superuser',
                'Manager MTPR',
                'MTPR',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [],

            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        // Gate for admin setting
        Gate::define('adminsetting', function ($user) {
            return $user->id === 1;
        });
    }
}

<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use Illuminate\Support\Facades\Hash as FacadesHash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionsAdmin_array = [];
        $permissionsViewer_array = [];
        $permissionsManager_array = [];

        $showPanelAdmin = Permission::create([
          'name' => 'show_panel_admin',
          'title' => 'ver_panel_administrador'
        ]);
        array_push($permissionsAdmin_array, $showPanelAdmin);
        array_push($permissionsViewer_array, $showPanelAdmin);
        array_push($permissionsManager_array, $showPanelAdmin);

        $seeBasedEntrance = Permission::create([
            'name' => 'see_based_entrance',
            'title' => 'ver_radicado_entrada'
        ]);
        array_push($permissionsAdmin_array, $seeBasedEntrance);
        array_push($permissionsViewer_array, $seeBasedEntrance);
        array_push($permissionsManager_array, $seeBasedEntrance);

        $createBasedEntrance = Permission::create([
            'name' => 'create_based_entrance',
            'title' => 'crear_radicado_entrada'
        ]);
        array_push($permissionsAdmin_array, $createBasedEntrance);
        array_push($permissionsManager_array, $createBasedEntrance);

        $editBasedEntrance = Permission::create([
            'name' => 'edit_based_entrance',
            'title' => 'editar_radicado_entrada'
        ]);
        array_push($permissionsAdmin_array, $editBasedEntrance);
        array_push($permissionsManager_array, $editBasedEntrance);

        $changeBasedEntranceStatus = Permission::create([
            'name' => 'change_based_entrance_status',
            'title' => 'cambiar_estado_radicado_entrada'
        ]);
        array_push($permissionsAdmin_array, $changeBasedEntranceStatus);
        array_push($permissionsManager_array, $changeBasedEntranceStatus);

        $seeBasedOut = Permission::create([
            'name' => 'see_based_out',
            'title' => 'ver_radicado_salida'
        ]);
        array_push($permissionsAdmin_array, $seeBasedOut);
        array_push($permissionsViewer_array, $seeBasedOut);
        array_push($permissionsManager_array, $seeBasedOut);

        $createBasedOut = Permission::create([
            'name' => 'create_based_out',
            'title' => 'crear_radicado_salida'
        ]);
        array_push($permissionsAdmin_array, $createBasedOut);
        array_push($permissionsManager_array, $createBasedOut);

        $editBasedOut = Permission::create([
            'name' => 'edit_based_out',
            'title' => 'editar_radicado_salida'
        ]);
        array_push($permissionsAdmin_array, $editBasedOut);
        array_push($permissionsManager_array, $editBasedOut);

        $changeBasedOutStatus = Permission::create([
            'name' => 'change_based_out_status',
            'title' => 'cambiar_estado_radicado_salida'
        ]);
        array_push($permissionsAdmin_array, $changeBasedOutStatus);
        array_push($permissionsManager_array, $changeBasedOutStatus);

        $searchBased = Permission::create([
            'name' => 'search_based',
            'title' => 'buscar_radicado'
        ]);
        array_push($permissionsAdmin_array, $searchBased);
        array_push($permissionsManager_array, $searchBased);

        $seeConfiguration = Permission::create([
          'name' => 'see_configuration',
          'title' => 'ver_configuracion'
        ]);
        array_push($permissionsAdmin_array, $seeConfiguration);
        array_push($permissionsManager_array, $seeConfiguration);

        $createDependence = Permission::create([
            'name' => 'create_dependence',
            'title' => 'crear_dependencias'
        ]);
        array_push($permissionsAdmin_array, $createDependence);
        array_push($permissionsManager_array, $createDependence);

        $editDependence = Permission::create([
            'name' => 'edit_dependence',
            'title' => 'editar_dependencias'
        ]);
        array_push($permissionsAdmin_array, $editDependence);
        array_push($permissionsManager_array, $editDependence);

        $changeStatusDependence = Permission::create([
            'name' => 'change_status_dependence',
            'title' => 'cambiar_estado_dependencias'
        ]);
        array_push($permissionsAdmin_array, $changeStatusDependence);
        array_push($permissionsManager_array, $changeStatusDependence);

        $exportDependence = Permission::create([
            'name' => 'export_dependence',
            'title' => 'exportar_dependencias'
        ]);
        array_push($permissionsAdmin_array, $exportDependence);
        array_push($permissionsManager_array, $exportDependence);

        $createDocumentType = Permission::create([
            'name' => 'create_document_type',
            'title' => 'crear_tipo_documento'
        ]);
        array_push($permissionsAdmin_array, $createDocumentType);
        array_push($permissionsManager_array, $createDocumentType);

        $editDocumentType = Permission::create([
            'name' => 'edit_document_type',
            'title' => 'editar_tipo_documento'
        ]);
        array_push($permissionsAdmin_array, $editDocumentType);
        array_push($permissionsManager_array, $editDocumentType);

        $changeDocumentTypeStatus = Permission::create([
            'name' => 'change_document_type_status',
            'title' => 'cambiar_estado_tipo_documento'
        ]);
        array_push($permissionsAdmin_array, $changeDocumentTypeStatus);
        array_push($permissionsManager_array, $changeDocumentTypeStatus);

        $exportDocumentType = Permission::create([
            'name' => 'export_document_type',
            'title' => 'exportar_tipo_documento'
        ]);
        array_push($permissionsAdmin_array, $exportDocumentType);
        array_push($permissionsManager_array, $exportDocumentType);

        $createPeopleType = Permission::create([
            'name' => 'create_people_type',
            'title' => 'crear_tipo_persona'
        ]);
        array_push($permissionsAdmin_array, $createPeopleType);
        array_push($permissionsManager_array, $createPeopleType);

        $editPeopleType = Permission::create([
            'name' => 'edit_people_type',
            'title' => 'editar_tipo_persona'
        ]);
        array_push($permissionsAdmin_array, $editPeopleType);
        array_push($permissionsManager_array, $editPeopleType);

        $changePeopleTypeStatus = Permission::create([
            'name' => 'change_people_type_status',
            'title' => 'cambiar_estado_tipo_persona'
        ]);
        array_push($permissionsAdmin_array, $changePeopleTypeStatus);
        array_push($permissionsManager_array, $changePeopleTypeStatus);

        $exportPeopleType = Permission::create([
            'name' => 'export_people_type',
            'title' => 'exportar_tipo_persona'
        ]);
        array_push($permissionsAdmin_array, $exportPeopleType);
        array_push($permissionsManager_array, $exportPeopleType);

        $createPrirority = Permission::create([
            'name' => 'create_prirority',
            'title' => 'crear_prioridades'
        ]);
        array_push($permissionsAdmin_array, $createPrirority);
        array_push($permissionsManager_array, $createPrirority);

        $editPrirority = Permission::create([
            'name' => 'edit_prirority',
            'title' => 'editar_prioridades'
        ]);
        array_push($permissionsAdmin_array, $editPrirority);
        array_push($permissionsManager_array, $editPrirority);

        $changeStatusPriority = Permission::create([
            'name' => 'change_status_priority',
            'title' => 'cambiar_estado_prioridades'
        ]);
        array_push($permissionsAdmin_array, $changeStatusPriority);
        array_push($permissionsManager_array, $changeStatusPriority);

        $exportPriority = Permission::create([
            'name' => 'export_priority',
            'title' => 'exportar_prioridades'
        ]);
        array_push($permissionsAdmin_array, $exportPriority);
        array_push($permissionsManager_array, $exportPriority);

        $createContextType = Permission::create([
            'name' => 'create_context_type',
            'title' => 'crear_tipo_contexto'
        ]);
        array_push($permissionsAdmin_array, $createContextType);
        array_push($permissionsManager_array, $createContextType);

        $editContextType = Permission::create([
            'name' => 'edit_context_type',
            'title' => 'editar_tipo_contexto'
        ]);
        array_push($permissionsAdmin_array, $editContextType);
        array_push($permissionsManager_array, $editContextType);

        $changeContextTypeStatus = Permission::create([
            'name' => 'change_context_type_status',
            'title' => 'cambiar_estado_tipo_contexto'
        ]);
        array_push($permissionsAdmin_array, $changeContextTypeStatus);
        array_push($permissionsAdmin_array, $changeContextTypeStatus);

        $exportContextType = Permission::create([
            'name' => 'export_context_type',
            'title' => 'exportar_tipo_contexto'
        ]);
        array_push($permissionsAdmin_array, $exportContextType);
        array_push($permissionsManager_array, $exportContextType);

        $createTypeIdentification = Permission::create([
            'name' => 'create_type_identification',
            'title' => 'crear_tipo_identificacion'
        ]);
        array_push($permissionsAdmin_array, $createTypeIdentification);
        array_push($permissionsManager_array, $createTypeIdentification);

        $editTypeIdentification = Permission::create([
            'name' => 'edit_type_identification',
            'title' => 'editar_tipo_identificacion'
        ]);
        array_push($permissionsAdmin_array, $editTypeIdentification);
        array_push($permissionsManager_array, $editTypeIdentification);

        $changeStatusTypeIdentification = Permission::create([
            'name' => 'change_status_type_identification',
            'title' => 'cambiar_estado_tipo_identificacion'
        ]);
        array_push($permissionsAdmin_array, $changeStatusTypeIdentification);
        array_push($permissionsManager_array, $changeStatusTypeIdentification);

        $exportTypeIdentification = Permission::create([
            'name' => 'export_type_identification',
            'title' => 'exportar_tipo_identification'
        ]);
        array_push($permissionsAdmin_array, $exportTypeIdentification);
        array_push($permissionsManager_array, $exportTypeIdentification);

        $createGenderType = Permission::create([
            'name' => 'create_gender_type',
            'title' => 'crear_tipo_generos'
        ]);
        array_push($permissionsAdmin_array, $createGenderType);
        array_push($permissionsManager_array, $createGenderType);

        $editGenderType = Permission::create([
            'name' => 'edit_gender_type',
            'title' => 'editar_tipo_generos'
        ]);
        array_push($permissionsAdmin_array, $editGenderType);
        array_push($permissionsManager_array, $editGenderType);

        $changeGenderTypeStatus = Permission::create([
            'name' => 'change_gender_type_status',
            'title' => 'cambiar_estado_tipo_genero'
        ]);
        array_push($permissionsAdmin_array, $changeGenderTypeStatus);
        array_push($permissionsManager_array, $changeGenderTypeStatus);

        $exportGenderType = Permission::create([
            'name' => 'export_gender_type',
            'title' => 'exportar_tipo_generos'
        ]);
        array_push($permissionsAdmin_array, $exportGenderType);
        array_push($permissionsManager_array, $exportGenderType);

        $createCancellationReason = Permission::create([
            'name' => 'create_cancellation_reason',
            'title' => 'crear_motivo_cancelacion'
        ]);
        array_push($permissionsAdmin_array, $createCancellationReason);
        array_push($permissionsManager_array, $createCancellationReason);

        $editCancellationReason = Permission::create([
            'name' => 'edit_cancellation_reason',
            'title' => 'editar_motivo_cancelacion'
        ]);
        array_push($permissionsAdmin_array, $editCancellationReason);
        array_push($permissionsManager_array, $editCancellationReason);

        $changeCancellationReasonStatus = Permission::create([
            'name' => 'change_cancellation_reason_status',
            'title' => 'cambiar_estado_motivo_cancelacion'
        ]);
        array_push($permissionsAdmin_array, $changeCancellationReasonStatus);
        array_push($permissionsManager_array, $changeCancellationReasonStatus);

        $exportCancellationReason = Permission::create([
            'name' => 'export_cancellation_reason',
            'title' => 'exportar_motivo_cancelacion'
        ]);
        array_push($permissionsAdmin_array, $exportCancellationReason);
        array_push($permissionsManager_array, $exportCancellationReason);

        $roles = Permission::create([
            'name' => 'manage_roles',
            'title' => 'administrar_roles'
        ]);
        array_push($permissionsAdmin_array, $roles);

        $createLegalRepresentative = Permission::create([
            'name' => 'create_legal_representative',
            'title' => 'crear_representate_legal'
        ]);
        array_push($permissionsAdmin_array, $createLegalRepresentative);
        array_push($permissionsManager_array, $createLegalRepresentative);

        $editLegalRepresentative = Permission::create([
            'name' => 'edit_legal_representative',
            'title' => 'editar_representate_legal'
        ]);
        array_push($permissionsAdmin_array, $editLegalRepresentative);
        array_push($permissionsManager_array, $editLegalRepresentative);

        $changeStatusLegalRepresentative = Permission::create([
            'name' => 'change_status_legal_representative',
            'title' => 'cambiar_estado_representate_legal'
        ]);
        array_push($permissionsAdmin_array, $changeStatusLegalRepresentative);
        array_push($permissionsManager_array, $changeStatusLegalRepresentative);

        $createCompany = Permission::create([
            'name' => 'create_company',
            'title' => 'crear_compania'
        ]);
        array_push($permissionsAdmin_array, $createCompany);
        array_push($permissionsManager_array, $createCompany);

        $editCompany = Permission::create([
            'name' => 'edit_company',
            'title' => 'editar_compania'
        ]);
        array_push($permissionsAdmin_array, $editCompany);
        array_push($permissionsManager_array, $editCompany);

        $changeCompanyStatus = Permission::create([
            'name' => 'change_company_status',
            'title' => 'cambiar_estado_compania'
        ]);
        array_push($permissionsAdmin_array, $changeCompanyStatus);
        array_push($permissionsManager_array, $changeCompanyStatus);

        $createCampus = Permission::create([
            'name' => 'create_campus',
            'title' => 'crear_sede'
        ]);
        array_push($permissionsAdmin_array, $createCampus);
        array_push($permissionsManager_array, $createCampus);

        $editCampus = Permission::create([
            'name' => 'edit_campus',
            'title' => 'editar_sede'
        ]);
        array_push($permissionsAdmin_array, $editCampus);
        array_push($permissionsManager_array, $editCampus);

        $changeCampusStatus = Permission::create([
            'name' => 'change_campus_status',
            'title' => 'cambiar_estado_sede'
        ]);
        array_push($permissionsAdmin_array, $changeCampusStatus);
        array_push($permissionsManager_array, $changeCampusStatus);

        $seeSettled = Permission::create([
            'name' => 'see_settled',
            'title' => 'ver_mis_radicados'
        ]);
        array_push($permissionsAdmin_array, $seeSettled);
        array_push($permissionsManager_array, $seeSettled);

        $viewUser = Permission::create([
            'name' => 'view_user',
            'title' => 'ver_usuarios'
        ]);
        array_push($permissionsAdmin_array, $viewUser);

        $createUser = Permission::create([
            'name' => 'create_user',
            'title' => 'crear_usuarios'
        ]);
        array_push($permissionsAdmin_array, $createUser);

        $editUser = Permission::create([
            'name' => 'edit_user',
            'title' => 'editar_usuarios'
        ]);
        array_push($permissionsAdmin_array, $editUser);

        $changeUserStatus = Permission::create([
            'name' => 'change_user_status',
            'title' => 'cambiar_estado_usuarios'
        ]);
        array_push($permissionsAdmin_array, $changeUserStatus);

        $printStamp = Permission::create([
          'name' => 'print_stamp',
          'guard_name' => 'web',
          'title' => 'imprimir_sello_radicado'
        ]);
        array_push($permissionsAdmin_array, $printStamp);

        $newPersonFromSettled = Permission::create([
          'name' => 'new_person_from_settled',
          'guard_name' => 'web',
          'title' => 'crear_persona_desde_radicado'
        ]);
        array_push($permissionsAdmin_array, $newPersonFromSettled);

        /* $role->givePermissionTo($permission);    asignar 1 solo permiso
        $role->syncPermissions($permissions);   asignar multiples permisos */

        /* creacion de roll */
        $superAdminRole = Role::create(['name' => 'super_admin']);
        /* asiganacion de permisos por array al roll */
        $superAdminRole->syncPermissions($permissionsAdmin_array);

        $viewerRole = Role::create(['name' => 'viewer']);
        $viewerRole->syncPermissions($permissionsViewer_array);

        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->syncPermissions($permissionsManager_array);

        $superAdminRole = Role::create(['name' => 'invited']);
        $superAdminRole = Role::create(['name' => 'auditor']);
        $superAdminRole = Role::create(['name' => 'basic']);

        //creamos el usuario super admin
        $userSuperAdmin = User::create([
            'username' => 'admin',
            'email' => 'diego30@soho.cl',
            'password' => FacadesHash::make('123456789'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $userViewer = User::create([
            'username' => 'viewer',
            'email' => 'diego3030@soho.cl',
            'password' => FacadesHash::make('123456789'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $userSuperAdmin->assignRole('super_admin');
        $userViewer->assignRole('viewer');

        // Reset cached roles and permissions
        //app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /* // create permissions
        Permission::create(['name' => 'edit articles']);
        Permission::create(['name' => 'delete articles']);
        Permission::create(['name' => 'publish articles']);
        Permission::create(['name' => 'unpublish articles']);

        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['name' => 'writer']);
        $role->givePermissionTo('edit articles');

        // or may be done by chaining
        $role = Role::create(['name' => 'moderator']);
        $role->givePermissionTo(['publish articles', 'unpublish articles']);

        $role = Role::create(['title' => 'super-admin']);
        $role->givePermissionTo(Permission::all()); */
    }
}

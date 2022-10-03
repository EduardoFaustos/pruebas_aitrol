<?php

namespace Sis_medico\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Svg\Tag\Group;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Sis_medico\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapWebRoutes_pentax();

        $this->mapWebRoutes_insumos();

        $this->mapWebRoutes_consultaM();

        $this->mapWebRoutes_hc_admision();

        $this->mapWebRoutes_empleados();

        $this->mapWebRoutes_hospital_iess();

        $this->mapWebRoutes_laboratorio();

        $this->mapWebRoutes_tecnicas();

        $this->mapWebRoutes_encuestas();

        $this->mapWebRoutes_cie_10();

        $this->mapWebRoutes_evolucion();

        $this->mapWebRoutes_preparacion();

        $this->mapWebRoutes_epicrisis();

        $this->mapWebRoutes_protocolo();

        $this->mapWebRoutes_procedimientos_historia();

        $this->mapWebRoutes_hc_cardiologia();
        $this->mapWebRoutes_hc_video();
        $this->mapWebRoutes_hc_consulta();
        $this->mapWebRoutes_web_orden();
        $this->mapWebRoutes_web_full_control();

        $this->mapWebRoutes_hc_receta();
        $this->mapWebRoutes_hc_visitas();
        $this->mapWebRoutes_sin_restriccion();
        $this->mapWebRoutes_web_facturacion();
        $this->mapWebRoutes_web_bo();

        $this->mapWebRoutes_web_hc4();
        $this->mapWebRoutes_web_call_center();
        $this->mapWebRoutes_web_enfermeria();
        $this->mapWebRoutes_web_reporte_subir();
        $this->mapWebRoutes_web_hospital();
        $this->mapWebRoutes_web_hc4_ordenes();
        $this->mapWebRoutes_web_hospital_admin();
        $this->mapWebRoutes_web_ieced();
        $this->mapWebRoutes_web_plan_cuentas();
        $this->mapWebRoutes_web_compra();
        $this->mapWebRoutes_web_as400();
        $this->mapWebRoutes_web_biopsias();
        $this->mapWebRoutes_web_diario();
        $this->mapWebRoutes_web_disponibilidad();
        //BANCOS
        $this->mapWebRoutes_nota_debito();
        $this->mapWebRoutes_nota_credito();
        $this->mapWebRoutes_transferencia_bancaria();
        $this->mapWebRoutes_deposito_bancario();
        $this->mapWebRoutes_conciliacion_bancaria();
        $this->mapWebRoutes_estadocuentabancos();

        $this->mapWebRoutes_nomina();
        $this->mapWebRoutes_web_ventas();
        $this->mapWebRoutes_web_clientes();
        $this->mapWebRoutes_web_financiero();

        

        $this->mapWebRoutes_web_balance_comprobacion();
        $this->mapWebRoutes_web_debito_bancario();
        $this->mapWebRoutes_web_estado_resultados();
        $this->mapWebRoutes_web_balance_general();
        $this->mapWebRoutes_web_flujo_efectivo();
        $this->mapWebRoutes_web_ats();

        $this->mapWebRoutes_web_af_mantenimientos();
        $this->mapWebRoutes_web_af_documentos();
        $this->mapWebRoutes_web_af_depreciaciones();
        $this->mapWebRoutes_web_af_informes();

        $this->mapWebRoutes_web_archivo_plano();

        $this->mapWebRoutes_web_clientes_retenciones();

        $this->mapWebRoutes_rol();

        $this->mapWebRoutes_vacunas();

        $this->mapWebRoutes_web_productos();

        $this->mapWebRoutes_web_kardex();

        $this->mapWebRoutes_limpieza();
        $this->mapWebRoutes_web_encuestaslabs();
        $this->mapWebRoutes_web_enfermeriaRiesgo();
        $this->mapWebRoutes_web_ServiciosGenerales();
        $this->mapWebRoutes_web_turnero();
        $this->mapWebRoutes_web_Emergencia();
        $this->mapWebRoutes_prueba();
        $this->mapWebRoutes_web_hospital_laboratorio();
        $this->mapWebRoutes_web_adelantado();
        $this->mapWebRoutes_web_ticketsop();
        $this->mapWebRoutes_web_ticketpermisos();
        $this->mapWebRoutes_web_controlsintoma();
        $this->mapWebRoutes_web_aud_hc_admision();
        $this->mapWebRoutes_web_importaciones();
        $this->mapWebRoutes_web_comercial();
        $this->mapWebRoutes_web_preparaciones();
        $this->mapWebRoutes_web_mantenimiento_prestamos();
        $this->mapWebRoutes_web_mantenimiento_nomina();
        //Nano
        $this->mapWebRoutes_pruebasx();
        //
        $this->mapWebRoutes_web_pdf_msp();

        //guia de remision
        $this->mapWebRoutes_web_guia_remision();


        //guia de remision
        $this->mapWebRoutes_web_guia_remision();


        $this->mapWebRoutes_web_sri_electronico();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    protected function mapWebRoutes_pentax()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/pentax/web_pentax.php'));
    }
    protected function mapWebRoutes_insumos()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/insumos/web_insumos.php'));
    }
    protected function mapWebRoutes_consultaM()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/consultam/web_consultam.php'));
    }
    protected function mapWebRoutes_hc_admision()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc_admision/web_hc_admision.php'));
    }
    protected function mapWebRoutes_evolucion()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc_admision/web_evolucion.php'));
    }
    protected function mapWebRoutes_preparacion()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc_admision/web_preparacion.php'));
    }
    protected function mapWebRoutes_epicrisis()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc_admision/web_epicrisis.php'));
    }
    protected function mapWebRoutes_empleados()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/rrhh/web_empleados.php'));
    }
    protected function mapWebRoutes_hospital_iess()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hospital_iess/web_hospital_iess.php'));
    }
    protected function mapWebRoutes_laboratorio()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/laboratorio/web_laboratorio.php'));
    }

    protected function mapWebRoutes_tecnicas()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/tecnicas_quirurgicas/tecnicas.php'));
    }

    protected function mapWebRoutes_encuestas()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/rrhh/encuestas.php'));
    }

    protected function mapWebRoutes_protocolo()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc_admision/web_protocolo.php'));
    }

    protected function mapWebRoutes_procedimientos_historia()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc_admision/procedimientos_historia.php'));
    }

    protected function mapWebRoutes_cie_10()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/cie_10/web_cie_10.php'));
    }

    protected function mapWebRoutes_hc_cardiologia()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc_admision/web_cardiologia.php'));
    }
    protected function mapWebRoutes_hc_video()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc_admision/web_video.php'));
    }

    protected function mapWebRoutes_hc_consulta()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc_admision/web_hc_consulta.php'));
    }
    protected function mapWebRoutes_hc_receta()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc_admision/web_receta.php'));
    }

    protected function mapWebRoutes_hc_visitas()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc_admision/visitas.php'));
    }

    protected function mapWebRoutes_web_orden()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc_admision/web_orden.php'));
    }
    protected function mapWebRoutes_web_full_control()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc_admision/web_full_control.php'));
    }

    protected function mapWebRoutes_sin_restriccion()
    {
        Route::namespace ($this->namespace)
            ->group(base_path('routes/sin_restriccion.php'));

    }
    
    protected function mapWebRoutes_web_facturacion()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/web_facturacion.php'));
    }

    protected function mapWebRoutes_web_bo()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/bo/web_bo.php'));
    }

    protected function mapWebRoutes_web_hc4()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc4/web_hc4.php'));
    }

    protected function mapWebRoutes_web_call_center()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/callcenter/web_call_center.php'));
    }
    protected function mapWebRoutes_web_reporte_subir()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/reportesubir/web_reporte_subir.php'));
    }

    protected function mapWebRoutes_web_clientes()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/web_clientes.php'));
    }
    protected function mapWebRoutes_web_enfermeria()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/enfermeria/web_enfermeria.php'));
    }
    protected function mapWebRoutes_web_hospital()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hospital/web_hospital.php'));
    }
    protected function mapWebRoutes_web_hospital_laboratorio()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hospital/laboratorio/web_laboratorio.php'));
    }
    protected function mapWebRoutes_web_ieced()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/ieced/web_ieced.php'));
    }
    protected function mapWebRoutes_web_hospital_admin()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hospital_admin/web_hospital_admin.php'));
    }

    protected function mapWebRoutes_web_plan_cuentas()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/web_plan_cuentas.php'));
    }
    protected function mapWebRoutes_web_compra()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/web_compra.php'));
    }

    protected function mapWebRoutes_web_hc4_ordenes()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc4/web_ordenes.php'));
    }

    protected function mapWebRoutes_web_as400()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/web_as400.php'));
    }

    protected function mapWebRoutes_web_biopsias()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/biopsias/web_biopsias.php'));
    }

    protected function mapWebRoutes_web_diario()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/web_diario.php'));
    }

     protected function mapWebRoutes_web_disponibilidad()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/disponibilidad/web_disponibilidad.php'));
    }

    protected function mapWebRoutes_nota_debito()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/banco/web_nota_debito.php'));
    }

    protected function mapWebRoutes_nota_credito()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/banco/web_nota_credito.php'));
    }

    protected function mapWebRoutes_nomina()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/web_nomina.php'));
    }

    protected function mapWebRoutes_web_balance_comprobacion()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/balance_comprobacion/web_balance_comprobacion.php'));
    }

    protected function mapWebRoutes_web_debito_bancario()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/banco/web_debito_bancario.php'));
    }

    protected function mapWebRoutes_web_estado_resultados()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/estado_resultado/web_estado_resultados.php'));
    }
    
    protected function mapWebRoutes_web_balance_general()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/balance_general/web_balance_general.php'));
    }

    protected function mapWebRoutes_web_flujo_efectivo()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/flujo_efectivo/web_flujo_efectivo.php'));
    }

    protected function mapWebRoutes_web_ats()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/web_ats.php'));
    }
    
    protected function mapWebRoutes_web_ventas()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/web_ventas.php'));
    }


    protected function mapWebRoutes_web_financiero()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/financiero/web_financiero.php'));
    }

    protected function mapWebRoutes_transferencia_bancaria()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/banco/web_deposito_bancario.php'));
    }

    protected function mapWebRoutes_deposito_bancario()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/banco/web_transferencia_bancaria.php'));
    }

    protected function mapWebRoutes_conciliacion_bancaria()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/banco/web_conciliacion_bancaria.php'));
    }

    protected function mapWebRoutes_estadocuentabancos()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/banco/web_estadocuentabancos.php'));
    }

    protected function mapWebRoutes_web_af_mantenimientos()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/activosfijos/web_af_mantenimientos.php'));
    }

    protected function mapWebRoutes_web_af_documentos()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/activosfijos/web_af_documentos.php'));
    }

    protected function mapWebRoutes_web_af_depreciaciones()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/activosfijos/web_af_depreciaciones.php'));
    }

    protected function mapWebRoutes_web_af_informes()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/activosfijos/web_af_informes.php'));
    }
    
    protected function mapWebRoutes_web_clientes_retenciones()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/clientes/web_retenciones.php'));
    }
    protected function mapWebRoutes_web_encuestaslabs()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/laboratorio/encuestas_labs.php'));
    }
    protected function mapWebRoutes_web_enfermeriaRiesgo()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/enfermeria/web_enfermeria_riesgo_caida.php'));
    }
    protected function mapWebRoutes_web_ServiciosGenerales()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/servicios_generales/servicios_generales.php'));
    }
    protected function mapWebRoutes_web_Emergencia()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hospital/emergencia/web_emergencia.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    
    protected function mapWebRoutes_web_archivo_plano()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/archivo_plano/web_archivo_plano.php'));
    }

    protected function mapWebRoutes_rol()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/rol.php'));
    }

    protected function mapWebRoutes_vacunas()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/vacunas.php'));
    }

    protected function mapWebRoutes_web_productos()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/web_productos.php'));
    }

    protected function mapWebRoutes_web_kardex()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/web_kardex.php'));
    }

    protected function mapWebRoutes_limpieza()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/limpieza_desinfeccion/limpieza.php'));
    }
    protected function mapWebRoutes_web_turnero()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/turnero/web_turnero.php'));
    }

    protected function mapWebRoutes_prueba()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/prueba1/prueba.php'));
    }

    protected function mapWebRoutes_pruebasx()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/pruebax/pruebasn.php'));
    }

     protected function mapWebRoutes_web_adelantado()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc_admision/web_adelantado.php'));
    }
    protected function mapWebRoutes_web_ticketsop()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/ticket_soporte_tecnico/web_ticketsop.php'));
    }

    protected function mapWebRoutes_web_aud_hc_admision()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/hc_admision/web_aud_hc_admision.php'));
    }

    protected function mapWebRoutes_web_ticketpermisos()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/permisos_ticket/web_permisos.php'));
    }

    protected function mapWebRoutes_web_controlsintoma()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/control_sintoma/web_sintoma.php'));
    }

    protected function mapWebRoutes_web_importaciones()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/web_importaciones.php'));
    }

    
    protected function mapWebRoutes_web_preparaciones()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/preparaciones/web_preparaciones.php'));
    }


    protected function mapWebRoutes_web_mantenimiento_prestamos()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/contable/mantenimiento_prestamos/web_mantenimiento_prestamos.php'));
    }
    
    protected function mapWebRoutes_web_comercial()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/comercial/web_comercial.php'));
    }
    protected function mapWebRoutes_web_mantenimiento_nomina()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/mantenimiento_nomina/web_mantenimiento_nomina.php'));
    }

    protected function mapWebRoutes_web_pdf_msp()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/msp/web_pdf.php'));
    }
    
    protected function mapWebRoutes_web_guia_remision()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/guia/web_guia_remision.php'));
    }


    
    
    protected function mapWebRoutes_web_sri_electronico()
    {
        Route::middleware('web', 'cors')
            ->namespace($this->namespace)
            ->group(base_path('routes/sri_electronico/web_sri_electronico.php'));
    }
}

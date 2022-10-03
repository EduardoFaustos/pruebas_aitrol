    public function carrito_pago($id_orden)//FUNCION QUE INVOCA LO DE JUANK
    {
        //return "hola";
        $orden = Examen_Orden::find($id_orden);
        $paciente = $orden->paciente;
        $id_paciente = $paciente->id;
        $grupo_fam = Labs_Grupo_Familiar::find($id_paciente);
        if (!is_null($grupo_fam)) {
            $user_aso = User::find($grupo_fam->id_usuario);
        } else {
            $user_aso = User::find($paciente->id_usuario);
        }
        $email = $orden->email_factura;
        if(is_null($orden)){
            return ['estado' => "Error"];
        }
        $valor_total = $orden->total_valor;
        /*
            INVOCA API DE PAGOS&FACTURAS Y RECIBE EL LINK DEL VPOS
        */    
        $RUC_LABS='0993075000001';
        $PAGOSYFACTURAS_APPID='V1oW1RHpw8GtxwGoIkuq';
        $PAGOSYFACTURAS_APPSECRET='SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';
        $_pasarela_pagos_subtotal = round($valor_total,2,PHP_ROUND_HALF_UP);
        $_pasarela_pagos_subtotal = round($_pasarela_pagos_subtotal * 100) / 100;
        //$_pasarela_pagos_subtotal = number_format($_pasarela_pagos_subtotal,2,'.','');
        $_pasarela_pagos_iva = 0;
        $_pasarela_pagos_total = round($valor_total,2,PHP_ROUND_HALF_UP);
        $_pasarela_pagos_total = round($_pasarela_pagos_total * 100) / 100;
        $pyf_checkout_url = 'https://vpos.accroachcode.com/';
        //$site_url = '192.168.75.51/sis_medico/public/laboratorio/externo/';
        //detalle(s)


        $datosAdicionales=array();
        array_push($datosAdicionales,array(
                        "key"   => "Agentes_Retencion", //los subgiones _ son espacios
                        "value" => "Resolucion Nro 1",
        ));
        array_push($datosAdicionales,array(
                        "key"   =>  "Paciente",
                        "value" =>  $orden->id_paciente." ".$this->cleanNames($orden->paciente->apellido1." ".$orden->paciente->nombre1),
        ));
        array_push($datosAdicionales,array(
                        "key"   =>  "Mail",
                        "value" =>  $this->cleanNames($email),
        ));
        array_push($datosAdicionales,array(
                        "key"   =>  "Ciudad",
                        "value" =>  $this->cleanNames($orden->ciudad_factura),
        ));
        array_push($datosAdicionales,array(
                        "key"   =>  "Direccion",
                        "value" =>  $orden->direccion_factura,
        ));





        $pyf_details=array();
        /*array_push($pyf_details,array(
            "sku"    =>  ''.$id_orden.'',
            "name"   =>  'LABS.EC Orden de laboratorio #'.$id_orden,
            "qty"    =>  1,
            "price"   =>  $_pasarela_pagos_subtotal,
            "tax"   =>  0.00,
            "discount"   =>  0.00,  //falta revisar
            "total"   =>  $_pasarela_pagos_subtotal,
        ));*/
        foreach ($orden->detalles as  $value) {
            array_push($pyf_details,array(
            "sku"    =>  ''.$id_orden.'-'.$value->id_examen,
            "name"   =>  $value->examen->nombre,
            "qty"    =>  1,
            "price"   =>  $value->valor,
            "tax"   =>  0.00,
            "discount"   =>  0.00,  //falta revisar
            "total"   =>  $value->valor,
            ));
        }
        
        $celular=$orden->telefono_factura;
        if($celular==null){
            $celular='0900000001';
        }
        if(strlen($celular)<10){
            $celular='0900000001';
        }
        if(!is_numeric($celular)){
            $celular='0900000001';   
        }
        // SI NO TIENE SEGUNDO APELLIDO O NOMBRE NO ENVIAR
        $nombres=$orden->nombre_factura;
        $nom= explode(" ", $nombres);
        $nom_cant= count($nom);
        if ($nom_cant == 4) {
            $xnombres =$nom[0].' '.$nom[1];
            $xsurname =$nom[2].' '.$nom[3];
        }elseif($nom_cant<4){
            $xnombres =$nom[0];
            $xsurname =$nom[1].' '.$nom[2];

        }elseif($nom_cant>4){
            $xnombres =$nom[0].' '.$nom[1];
            $xsurname =$nom[1].' '.$nom[2].' '.$nom_cant[4];

        }

        /*if($paciente->nombre2!=null && $paciente->nombre2!='(N/A)' && $paciente->nombre2!='N/A' && $paciente->nombre2!='.'){
            $nombres = $nombres.' '.$paciente->nombre2;
        }
        $apellidos=$paciente->apellido2;
        if($paciente->apellido2!=null && $paciente->apellido2!='(N/A)' && $paciente->apellido2!='N/A' && $paciente->apellido2!='.'){
            $apellidos = $apellidos.' '.$paciente->apellido2;
        }*/
        //json de invocación
        
        $data_array =  array(
            "company"        => $RUC_LABS,
            "person"         => array(
                "document"      => $orden->cedula_factura,
                "documentType"  => $this->getDocumentType($orden->cedula_factura),
                "name"          => $this->cleanNames(strtoupper($xnombres)),
                "surname"       => $this->cleanNames(strtoupper($xsurname)),
                "email"         => $email,
                "mobile"        => $celular,
                /*"address"       => array(
                    "street"    =>  $orden->direccion_factura,
                    "city"      =>  $orden->ciudad_factura,
                    "country"   =>  "EC"
                ), */            
            ),
            "paymentRequest"  => array(
                "orderId"       => ''.$id_orden.'',
                "description"   => "Compra en linea labs",  //PONER EN CONFIGURACION
                "items"         => array(
                    "item"        => $pyf_details //pending
                ),
                "amount"    =>  array(
                    "taxes"     =>  array(
                        array(
                            "kind"   =>  "Iva",
                            "amount"   =>  0.00,
                            "base"   =>  $_pasarela_pagos_subtotal,
                        )
                    ),
                    
                    "currency" => "USD",
                    "total" => $_pasarela_pagos_total,
                )
            ),
            "billingParameters" => array(
                "establecimiento"   => "001",
                "ptoEmision"        => "002",
                "infoAdicional" => $datosAdicionales,                
                "formaPago" =>  "19",
                "plazoDias" =>  "10"
            ),
            //"returnUrl" =>  "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_retorno_pago?orderid=".$id_orden,  //URL DE RETORNO
            //"cancelUrl" =>  "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_cancelacion_pago?orderid=".$id_orden, //URL DE CANCELACION
            "returnUrl" =>  "https://labs.ec/regresar.php",  //URL DE RETORNO
            "cancelUrl" =>  "https://labs.ec", //URL DE CANCELACION
            
            "userAgent" =>  "labs_ec/1"
        );
        
        $manage = json_encode($data_array);
        //return $manage; 
        $make_call = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/create/', $manage, $PAGOSYFACTURAS_APPID,$PAGOSYFACTURAS_APPSECRET);
        $response = json_decode($make_call, true);
        if($response['status']!=null){
            if($response['status']['status']=='success'){
                $pyf_checkout_url = $response['processUrl'];
            }
            else{
                $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status='.$response['status']['status'];
            }
        }
        else{
            $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=bad request';
        }
        /*
            FIN DE INVOCACION A API DE BOTON DE PAGOS
        */

           
        return ['estado' => 'ok', 'url_vpos' => $pyf_checkout_url]; //AGREGAMOS LA URL DE BOTON DE PAGOS
        //return ['estado' => 'ok', 'url_vpos' => "hola"]; //AGREGAMOS LA URL DE BOTON DE PAGOS
           
    }
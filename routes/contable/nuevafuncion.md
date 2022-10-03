### Proceso orden de venta
#### Tablas 
- ct_ven_orden
- ct_ven_orden_detalle

## Proceso
#### Cabecera
            $inputnew=[
                'nro_comprobante'     => $request['numero_comprobante'], //numero de comprobante
                'id_empresa'          => $request['empresa'], // id_empresa 
                'tipo'                => $request['tipo'], // es un campo varchar puede ser VEN-LABS
                'fecha'               => $request['fecha_asiento'], //fecha de envio
                'divisas'             => $request['divisas'], // clavale uno
                'nombre_cliente'      => $request['nombre_cliente'], // nombre del cliente en varchar
                'tipo_consulta'       => $request['tipo_consulta'], // este puede ser 1 o 0 consulta o procedimiento 
                'id_cliente'          => $request['identificacion_cliente'], //identificacion del cliente
                'direccion_cliente'   => $request['direccion_cliente'], //direccion del cliente
                'telefono_cliente'    => $request['telefono_cliente'], // telefono del cliente
                'email_cliente'       => $request['mail_cliente'], //mail del cliene
                'orden_venta'         => $request['orden_venta'], //el numero de orden, el id de la orden de laboratorio
                'estado_pago'         => '0', // default 0 
                'id_paciente'         => $request['identificacion_paciente'], // datos del paciente
                'nombres_paciente'    => $request['nombre_paciente'], //nombre del paciente
                'seguro_paciente'     => $request['id_seguro'], //seguro del paciente
                'copago'              => $request['copago'], // valor copago del paciente
                'id_recaudador'       => $request['cedula_recaudador'], //recaudador pero no es requerido
                'ci_vendedor'         => $request['cedula_vendedor'], // id del recuadador 
                'vendedor'            => $request['vendedor'], // vendedor pero no es requerido
                'subtotal_0'          => $request['subtotal_0'], //subtotal 0
                'subtotal_12'         => $request['subtotal_12'], //subtotal 12
                //'subtotal'                      => $request['subtotal1'],
                'descuento'           => $request['descuento'], // descuento 
                'base_imponible'      => $request['subtotal'], //subtotal
                'impuesto'            => $request['tarifa_iva'], // el valor del iva
                // 'transporte'                    => $request['transporte'],
                'total_final'         => $request['total'], //total de la factura
            ];
### Detalle
            $detalle = [
               
                'id_ct_productos'      => $request['codigo'],
                'nombre'               => $request['nombre'],
                'cantidad'             => $request['cantidad'],
                'precio'               => $request['precio'],
                'descuento_porcentaje' => $request['descpor'], // % de descuento
                'descuento'            => $request['descuento'],
                'extendido'            => $request['copago'],
                'detalle'              => $request['detalle'],
                'copago'               => $request['precioneto'],
                'check_iva'            => $request['iva'],
            ];
###  Ruta
#### c.enviocopago
#### contable/orden/facturacion
## Archivo de ruta
#### web_facturacion.php
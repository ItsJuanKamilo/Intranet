<tr>
    <td align="center" valign="top" width="100%" style="background-color: #f7f7f7;">
        <center>
            <table cellspacing="0" cellpadding="0" width="100%"
                   style="max-width: 600px; background-color: #ffffff; border-radius: 8px; font-family: Arial, sans-serif;">
                <!-- Encabezado -->
                <tr>
                    <td style="padding: 20px; text-align: center;">
                        @if($rma->lines_accepted == $rma->lines_total)
                            <img
                                src="https://artilec-chile.s3.dualstack.sa-east-1.amazonaws.com/web/rma/paso-rma-aceptado.png"
                                alt="RMA Aceptado" style="width: 100%; max-width: 600px; height: auto;">
                            <h1 style="color: #001c41; font-size: 22px; margin: 20px 0 10px;">
                                ¡Tu {{$rma->m_rmatype_id->name}} ha sido aceptada ✅!</h1>
                        @elseif($rma->lines_declined == $rma->lines_total)
                            <img
                                src="https://artilec-chile.s3.dualstack.sa-east-1.amazonaws.com/web/rma/paso-rma-rechazado.png"
                                alt="RMA Rechazada" style="width: 100%; max-width: 600px; height: auto;">
                            <h1 style="color: #001c41; font-size: 22px; margin: 20px 0 10px;">
                                ¡Tu {{$rma->m_rmatype_id->name}} ha sido rechazada ❌</h1>
                        @else
                            <img
                                src="https://artilec-chile.s3.dualstack.sa-east-1.amazonaws.com/web/rma/paso-rma-parcial.png"
                                alt="RMA Parcial" style="width: 100%; max-width: 600px; height: auto;">
                            <h1 style="color: #001c41; font-size: 22px; margin: 20px 0 10px;">
                                ¡Tu {{$rma->m_rmatype_id->name}} ha sido parcialmente aceptada ⚠!</h1>
                        @endif
                    </td>
                </tr>

                <!-- Respuesta -->
                <tr>
                    <td style="padding: 20px; font-size: 14px; color: #333333;">
                        <p style="margin-bottom: 15px; background-color: #fef8e7; border-left: 4px solid #ff9900; padding: 10px 15px;">
                            <strong>Respuesta de Artilec:</strong><br>
                            {!! $rma->solution_general !!}
                        </p>
                        <p style="margin-bottom: 0; background-color: #eaf6e9; border-left: 4px solid #2ab27b; padding: 10px 15px;">
                            <strong style="color: #2ab27b;">Retiro disponible:</strong> dirígete a <a
                                href="https://www.artilec.com/sucursales" target="_blank">Casa Matriz Huechuraba</a> con
                            el número de RMA <strong>{{$rma->documentno}}</strong> y el e-RUT de tu empresa.
                        </p>
                    </td>
                </tr>

                <!-- Información del Cliente y Vendedor -->
                <tr>
                    <td style="padding: 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; color: #333;">
                            <tr>
                                <td valign="top" width="50%" style="padding-right: 10px;">
                                    <strong style="color: #001c41;">Información del Cliente</strong>
                                    <ul style="padding: 0; list-style: none; margin: 10px 0;">
                                        <li><strong>Empresa:</strong> {{$rma->c_bpartner_id->name}}</li>
                                        <li><strong>RUT:</strong> {{$rma->c_bpartner_id->rut}}
                                            -{{$rma->c_bpartner_id->digito}}</li>
                                        @if($rma->c_bpartner_id->email_buyer)
                                            <li><strong>Email:</strong> <a
                                                    href="mailto:{{$rma->c_bpartner_id->email_buyer}}">{{$rma->c_bpartner_id->email_buyer}}</a>
                                            </li>
                                        @endif
                                        <li><strong>Ingreso:</strong> {{$rma->created}}</li>
                                        <li><strong>Finalización:</strong> {{$rma->updated}}</li>
                                    </ul>
                                    <strong style="color: #001c41;">Vendedor</strong>
                                    <ul style="padding: 0; list-style: none; margin: 10px 0;">
                                        <li>
                                            <strong>Nombre:</strong> {{ucfirst($rma->c_bpartner_id->salesrep_id->c_bpartner_id->name)}}
                                        </li>
                                        <li><strong>Email:</strong> <a
                                                href="mailto:{{$rma->c_bpartner_id->salesrep_id->email}}">{{$rma->c_bpartner_id->salesrep_id->email}}</a>
                                        </li>
                                        @if($rma->c_bpartner_id->salesrep_id->phone)
                                            <li><strong>Teléfono:</strong> <a
                                                    href="tel:{{$rma->c_bpartner_id->salesrep_id->phone}}">{{$rma->c_bpartner_id->salesrep_id->phone}}</a>
                                            </li>
                                        @endif
                                    </ul>
                                </td>
                                <td valign="top" width="50%" style="padding-left: 10px;">
                                    <strong style="color: #001c41;">Detalles del RMA</strong>
                                    <ul style="padding: 0; list-style: none; margin: 10px 0;">
                                        <li><strong>N° RMA:</strong> {{$rma->documentno}}</li>
                                        <li><strong>Entrega:</strong> {{$rma->inout_id->documentno}}</li>
                                        <li><strong>Tipo:</strong> {{$rma->m_rmatype_id->name}}</li>
                                        <li><strong>Sucursal:</strong> <a href="https://www.artilec.com/sucursales"
                                                                          target="_blank">{{$rma->ad_org_id->name}}</a>
                                        </li>
                                        <li><strong>Email RMA:</strong> {{$rma->contact_email}}</li>
                                        <li><strong>Técnico:</strong> {{$rma->salesrep_id->c_bpartner_id->name}}</li>
                                        <li><strong>Email Técnico:</strong> {{$rma->salesrep_id->email}}</li>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Solicitud de envío -->
                <tr>
                    <td style="padding: 0 20px 20px;">
                        <p style="font-size: 14px; color: #001c41;">
                            ¿Prefieres que enviemos tu pedido? <strong>Este servicio tiene un costo adicional.</strong>
                            Responde a este correo indicando lo que necesitas y nuestro equipo de ventas revisará tu
                            solicitud.
                        </p>
                    </td>
                </tr>

                <!-- Productos -->
                @if($rma->lines->count() > 0 && $rma->lines->count() < 4)
                    <tr>
                        <td style="padding: 20px;">
                            <h3 style="color: #365281; margin-bottom: 10px;">{{$rma->c_doctype_id->name}} {{$rma->m_rmatype_id->name}}
                                #{{$rma->documentno}}</h3>
                            <table width="100%" cellpadding="10" cellspacing="0"
                                   style="background-color: #f3f5f9; border-radius: 8px; font-size: 14px;">
                                <thead>
                                <tr style="background-color: #365281; color: #ffffff;">
                                    <th align="left">Producto</th>
                                    <th align="right">Cantidad</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($rma->lines as $l)
                                    <tr>
                                        <td style="vertical-align: top;">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="width: 100px; padding-right: 10px;">
                                                        <a href="https://www.artilec.com/{{$l->m_inoutline_id->m_product_id->url}}"
                                                           target="_blank">
                                                            <img
                                                                src="https://www.artilec.com/images/catalog/products/150x150/{{$l->m_inoutline_id->m_product_id->sku}}.jpg"
                                                                width="100" height="100"
                                                                alt="{{$l->m_inoutline_id->m_product_id->product}}"
                                                                style="border-radius: 4px; display: block;">
                                                        </a>
                                                    </td>
                                                    <td style="color: #333;">
                                                        <strong>{{$l->m_inoutline_id->m_product_id->product}}</strong><br>
                                                        {{$l->m_inoutline_id->m_product_id->model}} |
                                                        <a href="https://www.artilec.com/{{$l->m_inoutline_id->m_product_id->url}}"
                                                           target="_blank">{{$l->m_inoutline_id->m_product_id->sku}}</a><br>
                                                        @if(in_array($l->producttype->value, ['AC','RP']))
                                                            <span style="color: #2ab27b;">Estado: <strong>{{$l->producttype->name}} ✅</strong></span>
                                                            <br>
                                                            <span
                                                                style="font-size: 12px; color: #2ab27b;">{{$l->solution}}</span>
                                                        @else
                                                            <span style="color: darkred;">Estado: <strong>{{$l->producttype->name}} ❌</strong></span>
                                                            <br>
                                                            <span
                                                                style="font-size: 12px; color: darkred;">{{$l->solution}}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td align="center"
                                            style="font-size: 22px; font-weight: bold; color: #001c41;">{{abs($l->qty)}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endif
            </table>
        </center>
    </td>
</tr>

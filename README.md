# example-ovh-api
example order domain ovh (api v6)

# Consultar dominio 

```
# instancia de la clase
$ovhG = new OvhG();

# crear un carrito de compras
$orden = (object) $ovhG->order_cart_new('tu descripcion del carrito', '');

# informacion del dominio
$info_dominio 		= $ovhG->info_domain($orden->cartId, 'midominio.com');

# verificar si el dominio esta disponible para la compra

if($info_dominio[0]['orderable']) {
	print 'dominio disponible';
}
else {
	print 'dominio no disponible disponible';
}

```

# Compra de dominio 

```
# agregar el dominio a tu carrito
$ovhG->add_domain_to_cart($orden->cartId, 'midominio.com');

# asigno el carrito al usuario revendedor
$ovhG->add_cart_to_user($orden->cartId);

# valido el pedido de la compra a solicitar
$info_orden = $ovhG->validate_orden_cart($orden->cartId);

# realizo el pago del pedido en este caso con una cuenta de ovh
$ovhG->payment_orden($info_orden['orderId'], 'ovhAccount');

```

# Algunas configuraciones de dominio

```
# agregar dns al dominio
$info_zone = $ovhG->add_dns_to_domain('midominio.com', 'TXT', 'ovhcontrol', 'tu target', 0);

$id = $info_zone['id'];

# asignar hosting al dominio

$ovhG->add_hosting_to_domain('nombre de tu servicio', false, 'midominio.com', false, false, 'tu path', false);

```
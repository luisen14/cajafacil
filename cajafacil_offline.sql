--
-- File generated with SQLiteStudio v3.0.7 on jue mar 3 09:21:30 2016
--
-- Text encoding used: windows-1252
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: pagos
CREATE TABLE pagos (ID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, Valor float DEFAULT NULL, fkTipoPago int (11) NOT NULL DEFAULT (0), fkTicket int (11) NOT NULL DEFAULT (0), TipoCambio float DEFAULT NULL, PagoReal float DEFAULT NULL, NumCheque varchar (50) NOT NULL, Banco varchar (20) NOT NULL, cambio decimal (18, 2) DEFAULT (0));

-- Table: promociones
CREATE TABLE promociones (ID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, Nombre varchar (30) DEFAULT NULL, Descripcion varchar (30) NOT NULL DEFAULT '', FechaInicio date NOT NULL DEFAULT '0000-00-00', FechaFin date NOT NULL DEFAULT '0000-00-00', HoraInicio time NOT NULL DEFAULT '00:00:00', HoraFin time NOT NULL DEFAULT '00:00:00', Tipo char (1) DEFAULT NULL, Porcentaje decimal (12, 2) DEFAULT NULL, X decimal (12, 2) DEFAULT NULL, Y decimal (12, 2) DEFAULT NULL, DescuentoFijo decimal (12, 2) DEFAULT NULL, PrecioFijo decimal (12, 2) DEFAULT NULL, MaxCantidad float DEFAULT NULL, SoloAListaDePrecio int (11) DEFAULT '0', FkSucursal int (11) DEFAULT '0');

-- Table: prodcompxproddetalle
CREATE TABLE prodcompxproddetalle (
  ID     INTEGER 	PRIMARY KEY
                         NOT NULL,
  compID int(11)  DEFAULT NULL,
  DetID int(11)  DEFAULT NULL,
  cantidad decimal(15,2) DEFAULT NULL
);

-- Table: tipospago
CREATE TABLE tipospago (ID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, Nombre varchar (20) NOT NULL DEFAULT '', TipoCambio float NOT NULL DEFAULT '1', Active tinyint (3) DEFAULT NULL, ExactPay tinyint (3) DEFAULT NULL);
INSERT INTO tipospago (ID, Nombre, TipoCambio, Active, ExactPay) VALUES (1, 'Efectivo', 1, 1, 2);
INSERT INTO tipospago (ID, Nombre, TipoCambio, Active, ExactPay) VALUES (2, 'Vales', 1, 1, 2);
INSERT INTO tipospago (ID, Nombre, TipoCambio, Active, ExactPay) VALUES (3, 'Cheque', 1, 1, 1);
INSERT INTO tipospago (ID, Nombre, TipoCambio, Active, ExactPay) VALUES (4, 'D�lares', 11.17, 1, 2);
INSERT INTO tipospago (ID, Nombre, TipoCambio, Active, ExactPay) VALUES (5, 'Tarjeta de Credito', 1, 1, 1);
INSERT INTO tipospago (ID, Nombre, TipoCambio, Active, ExactPay) VALUES (6, 'Cr�dito', 1, 1, 1);

-- Table: codigosbarra
CREATE TABLE codigosbarra (ID INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, Codigo VARCHAR (30), fkProducto int (11), Tipo VARCHAR (12), multiplicador decimal (10, 2), nombrealt varchar (60), temporal tinyint (3), descripcion varchar (100), plu int (10));

-- Table: departamentospromocion
CREATE TABLE `departamentospromocion` (
  `fkDepartamento` int(11)  NOT NULL DEFAULT '0',
  `fkPromocion` int(11)  NOT NULL DEFAULT '0');

-- Table: tickets
CREATE TABLE tickets (ID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, Numero int (11) DEFAULT NULL, Abierto tinyint (3) DEFAULT NULL, Pagado tinyint (3) DEFAULT NULL, Cancelado tinyint (3) DEFAULT NULL, Facturado tinyint (3) DEFAULT NULL, Suspendido int (11) NOT NULL DEFAULT '0', Fecha date NOT NULL DEFAULT '0000-00-00', Total double NOT NULL DEFAULT '0', IVA double DEFAULT NULL, fkUsuario int (11) DEFAULT NULL, fkCliente int (11) DEFAULT NULL, fkCaja int (11) DEFAULT NULL, Saldo decimal (15, 2) NOT NULL DEFAULT '0.00', Ahorro double NOT NULL DEFAULT '0', Hora time DEFAULT '00:00:00', Comentario varchar (27) DEFAULT NULL, fechaVence datetime DEFAULT NULL, Autorizo varchar (200) DEFAULT 'No Requirio', Mensaje varchar (250) DEFAULT NULL, Pedido int (11) DEFAULT NULL);

-- Table: configcaja
CREATE TABLE configcaja (ID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ImpresoraTickets varchar (200), PuertoImpTickets varchar (20), ImpresoraEtiquetas varchar (200), CodigoCorte varchar (20), CodigoAperturaCajon varchar (20), DispLectura1 varchar (200), DispFrec1 varchar (20), DispStop1 varchar (20), DispPar1 varchar (20), DispFlow1 varchar (20), DispData1 varchar (20), DisplayEnabled1 tinyint (3) NOT NULL, DispLectura2 varchar (200) DEFAULT (0), DispFrec2 varchar (20), DispStop2 varchar (20), DispPar2 varchar (20), DispFlow2 varchar (20), DispData2 varchar (20), DispAssci2 varchar (20), fkConfigticketcompra int (11), fkConfigticketcortex int (11), LectAut2 tinyint (3) DEFAULT (1), LimAlert decimal (18, 2), LimOper decimal (18, 2), BloqLimOper tinyint (3) DEFAULT (0), BloqOnExistZero tinyint (3) DEFAULT (1), showCosts tinyint (3) DEFAULT (1), BehaveVendor varchar (20), fkUsuario int (11), copiesPrecuenta int (11) DEFAULT (1), fkTipoPago int (11), ShowCashDWDialog tinyint (3) DEFAULT (1), pedidos tinyint (3) DEFAULT (0), pedidosimprime tinyint (3) DEFAULT (0), fkCaja int (11), facturas tinyint (3) DEFAULT (0), etiquetas tinyint (3) DEFAULT (0), ticketsbasculas tinyint (3) DEFAULT (0), sincbasculas tinyint (3) DEFAULT (0), copiascredito tinyint (3) DEFAULT (1), copiaspedidos tinyint (3) DEFAULT (1), reanudar tinyint (3) DEFAULT (1), copiasabonos tinyint (3) DEFAULT (1), ImpresoraReportes varchar (200));
INSERT INTO configcaja (ID, ImpresoraTickets, PuertoImpTickets, ImpresoraEtiquetas, CodigoCorte, CodigoAperturaCajon, DispLectura1, DispFrec1, DispStop1, DispPar1, DispFlow1, DispData1, DisplayEnabled1, DispLectura2, DispFrec2, DispStop2, DispPar2, DispFlow2, DispData2, DispAssci2, fkConfigticketcompra, fkConfigticketcortex, LectAut2, LimAlert, LimOper, BloqLimOper, BloqOnExistZero, showCosts, BehaveVendor, fkUsuario, copiesPrecuenta, fkTipoPago, ShowCashDWDialog, pedidos, pedidosimprime, fkCaja, facturas, etiquetas, ticketsbasculas, sincbasculas, copiascredito, copiaspedidos, reanudar, copiasabonos, ImpresoraReportes) VALUES (1, 'PDF', '', 'PDF', '', '', 'Sin Lector', '9600', '1', 'Ninguna', 'XonXoff', '8', 0, 'Sin Bascula', '9600', '1', 'Par', 'Ninguno', '7', '87', 1, 1, 0, 99999999, 99999999, 0, 0, 1, 'Segun Usuario', 1, 0, 1, 0, 1, 1, 1, 0, 0, 0, 0, 1, 1, 1, 1, 'PDF');

-- Table: clientes
CREATE TABLE clientes (ID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, Clave VARCHAR (15), Nombre VARCHAR (250), Tel1 varchar (20), Tel2 varchar (20), Tel3 varchar (20), Tel4 varchar (20), eMail varchar (250), Direccion varchar (200), calle varchar (150), numExt varchar (40), numInt varchar (40), Entre1 varchar (50), Entre2 varchar (50), Colonia varchar (30), Ciudad varchar (30), Estado varchar (20), CP varchar (10), Pais varchar (40), Referencia varchar (250), fkListaPrecios int (10), RFC varchar (20), Descuento decimal (10, 2), limiteCredito decimal (15, 2) DEFAULT (0), diasPlazo int (11) DEFAULT (15), crestringido tinyint (3), activo tinyint (3), DescuentoEspecial decimal (10, 2) DEFAULT (0), FkSucursal int (11) DEFAULT (0), essucursal int (11));
INSERT INTO clientes (ID, Clave, Nombre, Tel1, Tel2, Tel3, Tel4, eMail, Direccion, calle, numExt, numInt, Entre1, Entre2, Colonia, Ciudad, Estado, CP, Pais, Referencia, fkListaPrecios, RFC, Descuento, limiteCredito, diasPlazo, crestringido, activo, DescuentoEspecial, FkSucursal, essucursal) VALUES (1, '0', 'P�blico general', '', '', '', '', '', '1 1 1', '1', '1', '1', '1', '', '1', '1', '1', '', 'MEXICO', '', 1, 'XAXX010101000', 0, 0, 0, 0, 1, 0, 0, NULL);

-- Table: productospromocion
CREATE TABLE productospromocion (ID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, fkProducto int (11) NOT NULL DEFAULT '0', fkPromocion int (11) NOT NULL DEFAULT '0', fkCodigo int (11) DEFAULT NULL);

-- Table: configpdv
CREATE TABLE configpdv (
      ID     INTEGER  PRIMARY KEY AUTOINCREMENT
                         NOT NULL,
  KindLimitPrice varchar(4) ,
  ImprimirTicketsSusp boolean ,
  MinusEnabled tinyint(3) ,
  PlusEnabled tinyint(3) ,
  ForceRowPerEqualProduct tinyint(3) ,
  allowAddMessage tinyint(3) ,
  mensaje1 varchar(250) ,
  mensaje2 varchar(250) ,
  mensaje3 varchar(250) ,
  mensaje4 varchar(250) ,
  mensaje5 varchar(250) ,
  fkCaja int(11) 
);
INSERT INTO configpdv (ID, KindLimitPrice, ImprimirTicketsSusp, MinusEnabled, PlusEnabled, ForceRowPerEqualProduct, allowAddMessage, mensaje1, mensaje2, mensaje3, mensaje4, mensaje5, fkCaja) VALUES (1, 'None', 1, 1, 1, 0, 2, '', '', '', '', '', 1);

-- Table: productosticket
CREATE TABLE productosticket (ID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, fkTicket int (11) NOT NULL DEFAULT ('0'), fkProducto int (11) NOT NULL DEFAULT ('0'), PrecioUnitario double NOT NULL DEFAULT ('0'), Cantidad decimal (15, 4) NOT NULL DEFAULT ('0'), PorcIVA int (11) DEFAULT NULL, FkPromo int (11) DEFAULT NULL, PorcImp2 int (11) DEFAULT NULL, PorcImp3 int (11) DEFAULT NULL, PorcImp4 int (11) DEFAULT NULL, PorcImp5 int (11) DEFAULT NULL, fkserie int (10) DEFAULT NULL, comision decimal (15, 2) DEFAULT NULL, Vendor int (11) DEFAULT NULL, montoSImp decimal (15, 2) DEFAULT NULL, montoIVA decimal (15, 2) DEFAULT NULL, montoIMP2 decimal (15, 2) DEFAULT NULL, montoIMP3 decimal (15, 2) DEFAULT NULL, montoIMP4 decimal (15, 2) DEFAULT NULL, montoIMP5 decimal (15, 2) DEFAULT NULL, montoTotal decimal (15, 2) DEFAULT NULL, fkListaVenta int (10) DEFAULT NULL, listaVenta varchar (250) DEFAULT NULL, precioLista1 decimal (15, 2) DEFAULT NULL, hora datetime NOT NULL, fkCodigo int (11) DEFAULT NULL, fkTicketE varchar (20) DEFAULT NULL);

-- Table: productoslistas
CREATE TABLE productoslistas (ID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, fkLista int (10) NOT NULL DEFAULT ('0'), fkProducto int (10) DEFAULT NULL, arribade decimal (15, 2) DEFAULT NULL, precio decimal (15, 2) DEFAULT NULL, fkCodigo int (10) NOT NULL DEFAULT ('0'), fkUsuario int (11) DEFAULT NULL);

-- Table: productos
CREATE TABLE productos (ID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, NombreCorto varchar (25) NOT NULL DEFAULT '', NombreLargo varchar (60) NOT NULL DEFAULT '', NumeroSerie varchar (30) DEFAULT NULL, porIVA decimal (12, 2) DEFAULT NULL, porIEPS decimal (12, 2) DEFAULT NULL, porSunt decimal (12, 2) DEFAULT NULL, porIMP4 decimal (12, 2) DEFAULT NULL, porIMP5 decimal (12, 2) DEFAULT NULL, Pesado tinyint (3) DEFAULT NULL, fkTipoPago int (10) DEFAULT NULL, Compuesto tinyint (3) DEFAULT NULL, Comision decimal (4, 2) DEFAULT NULL, aplicaDctoEsp tinyint (3) DEFAULT NULL, validapreciovolumen tinyint (3) DEFAULT ('1'), exentoiva tinyint (3) DEFAULT NULL, ventafraccionada tinyint (3) DEFAULT NULL);

-- Table: lineaspromocion
CREATE TABLE `lineaspromocion` (
  `fkLinea` int(11)  NOT NULL DEFAULT '0',
  `fkPromocion` int(11)  NOT NULL DEFAULT '0');

-- Table: contenidocaja
CREATE TABLE contenidocaja (
  fkCaja int(11)  NOT NULL DEFAULT (0),
  fkTipoPago int(11)  NOT NULL DEFAULT (0),
  Valor decimal(15,5) NOT NULL DEFAULT (0),
  PRIMARY KEY (fkCaja,fkTipoPago)  
);
INSERT INTO contenidocaja (fkCaja, fkTipoPago, Valor) VALUES (1, 1, 0);
INSERT INTO contenidocaja (fkCaja, fkTipoPago, Valor) VALUES (1, 2, 0);
INSERT INTO contenidocaja (fkCaja, fkTipoPago, Valor) VALUES (1, 3, 0);
INSERT INTO contenidocaja (fkCaja, fkTipoPago, Valor) VALUES (1, 4, 0);
INSERT INTO contenidocaja (fkCaja, fkTipoPago, Valor) VALUES (1, 5, 0);
INSERT INTO contenidocaja (fkCaja, fkTipoPago, Valor) VALUES (1, 6, 0);

-- Table: listasprecio
CREATE TABLE listasprecio (ID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nombre varchar (250) DEFAULT NULL, FkSucursal int (11) DEFAULT (0));
INSERT INTO listasprecio (ID, nombre, FkSucursal) VALUES (1, 'Precios 1', 1);
INSERT INTO listasprecio (ID, nombre, FkSucursal) VALUES (2, 'Precios 2', 1);
INSERT INTO listasprecio (ID, nombre, FkSucursal) VALUES (3, 'Precios 3', 1);
INSERT INTO listasprecio (ID, nombre, FkSucursal) VALUES (4, 'Precios 4', 1);
INSERT INTO listasprecio (ID, nombre, FkSucursal) VALUES (5, 'Precios 5', 1);

-- Table: configticketcompra
CREATE TABLE configticketcompra (ID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, MensajeInicial varchar (255) NOT NULL, MensajeFinal varchar (255) NOT NULL, MostrarMensajeInicial tinyint (3) DEFAULT NULL, MostrarMensajeFinal tinyint (3) DEFAULT NULL, MostrarImagen tinyint (3) DEFAULT NULL, MostrarPublicidad tinyint (3) DEFAULT NULL, Imagen longtext, Publicidad longtext, Descripcion tinyint (3) DEFAULT NULL, Codigo tinyint (3) DEFAULT NULL, Cantidad tinyint (3) DEFAULT NULL, PrecioUnitario tinyint (3) DEFAULT NULL, Total tinyint (3) DEFAULT NULL, IVA tinyint (3) DEFAULT NULL, TotalEnLetra tinyint (3) DEFAULT NULL, TotalDeProductos tinyint (3) DEFAULT NULL, Cambio tinyint (3) DEFAULT NULL, MostrarCaja tinyint (3) DEFAULT NULL, MostrarCajero tinyint (3) DEFAULT NULL, Password varchar (40) DEFAULT NULL, Imp2 tinyint (3) DEFAULT NULL, Imp3 tinyint (3) DEFAULT NULL, Imp4 tinyint (3) DEFAULT NULL, Imp5 tinyint (3) DEFAULT NULL, copias int (10) DEFAULT '0', tktschema varchar (200) DEFAULT ('Sin Nombre'), MostrarFolio tinyint (3) DEFAULT NULL, FolioCB tinyint (3) DEFAULT NULL, ImageIndex tinyint (3) DEFAULT (0));
INSERT INTO configticketcompra (ID, MensajeInicial, MensajeFinal, MostrarMensajeInicial, MostrarMensajeFinal, MostrarImagen, MostrarPublicidad, Imagen, Publicidad, Descripcion, Codigo, Cantidad, PrecioUnitario, Total, IVA, TotalEnLetra, TotalDeProductos, Cambio, MostrarCaja, MostrarCajero, Password, Imp2, Imp3, Imp4, Imp5, copias, tktschema, MostrarFolio, FolioCB, ImageIndex) VALUES (1, 'Bienvenidos', 'Gracias por su compra', 1, 1, 0, 0, 'conf_logo_1.bmp', 'conf_pagina_1.bmp', 1, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, '464ec91d4b7337522732e3ef9383b82873f1a188', 0, 0, 0, 0, 0, 'Sin Nombre', 1, 1, 1);

COMMIT TRANSACTION;
PRAGMA foreign_keys = on;

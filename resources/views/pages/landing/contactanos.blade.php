@extends('layouts.landing')

@section('content')
 <!-- Breadcrumb -->
<div class="container mx-auto px-4 py-3 bg-gray-200">
      <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <a
              href="{{ route('landing') }}"
              class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600"
            >
              <i class="fas fa-home mr-2"></i>
              Inicio
            </a>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <i class="fas fa-angle-right text-gray-400 mx-2"></i>
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Contactanos</span>
            </div>
          </li>
        </ol>
      </nav>
    </div>

      <!--Main -->
      <title>¿Dónde nos encontramos?</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
    }

    .banner {
      background: linear-gradient(to bottom, #999ca1, #f1f1f1);
      text-align: center;
      padding: 40px 20px;
      border: 2px solid #000;
    }

    .banner h1 {
      margin: 0;
      font-size: 48px;
      font-weight: 900;
      color: #111;
    }

    .banner p {
      margin-top: 10px;
      font-size: 18px;
      color: #111;
    }
  </style>
</head>
<body>
  <div class="banner">
    <h1>¿DONDE NOS ENCONTRAMOS?</h1>
    <p>DATOS DE CONTACTO</p>
  </div>
</body>


 <title>Contacto</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: #ffffff;
    }

    .contacto-container {
      display: flex;
      flex-wrap: wrap;
      padding: 40px;
      justify-content: center;
      gap: 20px;
    }

    .datos-contacto {
      background-color: #f2f2f2;
      padding: 30px;
      max-width: 500px;
      flex: 1 1 400px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .datos-contacto h2 {
      font-size: 36px;
      margin-bottom: 20px;
      font-weight: 900;
    }

    .datos-contacto p {
      font-size: 18px;
      margin: 8px 0;
    }

    .datos-contacto strong {
      font-weight: 700;
    }

    .mapa {
      flex: 1 1 400px;
    }

    .mapa iframe {
      width: 100%;
      height: 450px;
      border: none;
    }

    @media (max-width: 768px) {
      .contacto-container {
        flex-direction: column;
        align-items: center;
      }

      .mapa iframe {
        height: 300px;
      }
    }
  </style>
</head>
<body>

  <div class="contacto-container">
    <div class="datos-contacto">
      <h2>Datos de contacto</h2>
      <p><strong>Número Principal:</strong> <span style="color:#000;"><strong>+57</strong> 311 7337272</span></p>
      <p><strong>Número Servicio Técnico:</strong> <span style="color:#000;"><strong>+57</strong> 323 6331212</span></p>
      <p><strong>Dirección:</strong> Cra 52 #49-100, La Candelaria</p>
      <p><strong>Sitio web:</strong> <a href="https://www.4gmovil.com.co" target="_blank">www.4gmovil.com.co</a></p>
      <br>
      <p><strong>Horario de apertura</strong></p>
      <p>Lunes a sábado: 9:00 a. m. a 7:00 p. m.</p>
      <p>Domingo: cerrado</p>
    </div>

    <div class="mapa">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.7797588889546!2d-75.57204782585938!3d6.245693993755887!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e4429a4a190c79d%3A0x5a14782df8603f0d!2sCra.%2052%20%2349-100%2C%20La%20Candelaria%2C%20Medell%C3%ADn%2C%20Antioquia!5e0!3m2!1ses-419!2sco!4v1716409909321!5m2!1ses-419!2sco"
        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </div>

</body>


<title>Formulario de Contacto</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: #fff;
    }

    .formulario-contacto {
      max-width: 700px;
      margin: 80px auto;
      padding: 40px;
      background-color: #8a8d91;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      color: #000;
    }

    .formulario-contacto h2 {
      text-align: center;
      font-size: 48px;
      font-weight: 900;
      margin-bottom: 30px;
    }

    .formulario-contacto input,
    .formulario-contacto textarea {
      width: 100%;
      padding: 15px;
      margin: 10px 0;
      border: none;
      border-bottom: 4px solid #666;
      font-size: 16px;
      background-color: #fff;
      color: #333;
    }

    .formulario-contacto input:focus,
    .formulario-contacto textarea:focus {
      outline: none;
      border-bottom-color: #000;
    }

    .formulario-contacto textarea {
      height: 150px;
      resize: vertical;
    }

    .formulario-contacto button {
      background-color: #d4d7dd;
      color: #000;
      padding: 10px 20px;
      font-size: 16px;
      border: none;
      cursor: pointer;
      margin-top: 10px;
    }

    .formulario-contacto button:hover {
      background-color: #c0c3c8;
    }

    @media (max-width: 600px) {
      .formulario-contacto h2 {
        font-size: 36px;
      }
    }
  </style>
</head>
<body>

  <div class="formulario-contacto">
    <h2>Contáctenos</h2>
    <form action="#" method="post">
      <input type="text" name="nombre" placeholder="Enter your Name" required>
      <input type="email" name="email" placeholder="Enter a valid email address" required>
      <textarea name="mensaje" placeholder="Enter your message" required></textarea>
      <button type="submit">Enviar</button>
    </form>
  </div>

</body>

 

    <!-- Fin Main-->
@endsection 
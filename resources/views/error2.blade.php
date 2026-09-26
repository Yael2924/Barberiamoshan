<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página o acción no encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .error-container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url("/img/bar1.jpg");
            color: #000;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            padding: 20px;
            box-sizing: border-box;
        }

        .error-box {
            background-color: rgba(255, 255, 255, 0.697); 
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .error-icon {
            font-size: 8rem;
            animation: float 3s ease-in-out infinite;
            color: #4CAF50;
        }

        .error-code {
            font-size: 5rem;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .btn-home {
            background: #4CAF50;
            color: white;
            padding: 15px 40px;
            border-radius: 30px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(76,175,80,0.4);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .parrafo {
            font-size: 1.1rem;
            font-weight: bold;
        }

        .t-error {
            font-size: 3rem;
            font-weight: bold;
        }

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .error-icon {
                font-size: 6rem;
            }

            .error-code {
                font-size: 4rem;
            }

            .t-error {
                font-size: 2.5rem;
            }

            .parrafo {
                font-size: 1rem;
            }

            .btn-home {
                padding: 10px 30px;
            }
        }

        @media (max-width: 480px) {
            .error-icon {
                font-size: 4rem;
            }

            .error-code {
                font-size: 3rem;
            }

            .t-error {
                font-size: 2rem;
            }

            .parrafo {
                font-size: 0.9rem;
            }

            .btn-home {
                padding: 8px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-box text-center">
            <i class="fas fa-exclamation-triangle error-icon mb-4" style="color: red;"></i>
            <h1 class="error-code mb-3">404</h1>
            <h2 class="mb-4 t-error">Página no encontrada</h2>
            <p class="parrafo lead mb-5">Lo sentimos, la página o la acción que estás buscando no existe, ha sido movida o actualizada.</p>
            <!-- <a href="/" class="btn btn-home" style="background-color: black;">
                <i class="fas fa-home me-2"></i>Volver al inicio
            </a> -->
        </div>
    </div>
</body>
</html>
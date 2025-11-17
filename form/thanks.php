<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="7;url=/" />
    <title>Thank You - Brown Business Intelligence</title>
    
    <!-- Bootstrap -->
    <link href="../css/bootstrap-4.4.1.BBI001.css" rel="stylesheet">
    <link href="../css/particle.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/custom.css?dt=<?php echo time();?>" />
    
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }
        
        .content-wrapper {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        
        .thank-you-card {
            background-color: rgba(240, 237, 235, 0.95);
            border-radius: 1rem;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            padding: 3rem;
            max-width: 600px;
            text-align: center;
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .thank-you-card h1 {
            color: #2c3e50;
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        
        .thank-you-card .checkmark {
            color: #28a745;
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        .thank-you-card p {
            color: #495057;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        
        .thank-you-card .btn {
            background-color: #007bff;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            border: 2px solid #007bff;
        }
        
        .thank-you-card .btn:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,123,255,0.3);
        }
        
        .redirect-notice {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <!-- Particles Background -->
    <div id="particles-js"></div>
    
    <!-- Content -->
    <div class="content-wrapper">
        <div class="thank-you-card">
            <div class="checkmark">✓</div>
            <h1>Thank You!</h1>
            <p>
                We've received your message and appreciate you reaching out. 
                Someone from our team will be in contact with you shortly to discuss 
                your healthcare AI implementation needs.
            </p>
            <a href="../index.php" class="btn">Return to Home</a>
            <p class="redirect-notice">You'll be automatically redirected in <span id="countdown">7</span> seconds...</p>
            <script>
              let seconds = 7;
              const countdownEl = document.getElementById('countdown');
              const timer = setInterval(() => {
                seconds--;
                countdownEl.textContent = seconds;
                if (seconds <= 0) {
                  clearInterval(timer);
                }
              }, 1000);
            </script>
        </div>
    </div>
    
    <!-- Particles.js -->
    <script src="../js/particles.js"></script>
    <script src="../js/app.js"></script>
</body>
</html>



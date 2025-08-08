<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f1f1f1; 
    color: #2c3e50;
    background-image: url('public-img/img.jpeg');
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
}

a {
    font-size: 1.5rem;
    color: #ff9613;
    text-decoration: none;
    background-color: black;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.2s ease;
    box-shadow: 0 4px 8px rgba(255, 255, 255, 0.1);
}

a:hover {
    background-color:rgb(9, 255, 0);
    color: #fff;
    transform: scale(1.05);
}


    </style>
</head>
<body>
    <a href="login.php">Bem-Vindo(a)!</a>
</body>
</html>
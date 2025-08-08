<?php
session_start();
require '../../config/conexao.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🦘Game 🍺</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-image: url('../../adwsifgbwioq78657gjhkj/midia/cdn/imagem2.php');
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            margin: 0;
            font-family: 'Arial', sans-serif;
            color: #ff9613;
            overflow: hidden;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 36px;
            font-weight: bold;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
            background-color: black;
        }

        #game-board {
            position: relative;
            width: 400px;
            height: 400px;
            background-color: #87d532;
            border-radius: 12px;
            border: 4px solid #ecf0f1;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        .canguru {
            position: absolute;
            width: 20px;
            height: 20px;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ecf0f1;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .food {
            position: absolute;
            width: 20px;
            height: 20px;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f39c12;
        }

        #score {
            font-size: 26px;
            font-weight: bold;
            color: #ff9613;
            margin-top: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            background-color: black;
        }

        .game-over {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 32px;
            color: #e74c3c;
            font-weight: bold;
            text-align: center;
            text-shadow: 3px 3px 10px rgba(0, 0, 0, 0.7);
        }
    </style>
    <script>
        window.onload = function() {
        if (window.innerWidth < 1024) {
            alert('Esta página funciona apenas em telas grandes.');
            window.history.back();
        }
        };
    </script>
</head>
<body>
    <script>
        alert('Aperte OK para iniciar.');
    </script>
    <h1>Canguru - Depósito de Bebidas</h1>
    <div id="game-board"></div>
    <div id="score">Score: 0</div>
    <br><br><br><br><br><br><br><br>
    <a href="../dashboard.php" style=""><div id="score">Voltar</div></a>
    

    <script>
        let board = document.getElementById('game-board');
        const scoreDisplay = document.getElementById('score');
        const boardSize = 20;
        let canguru = [{ x: 10, y: 10 }];
        let direction = { x: 1, y: 0 };
        let newDirection = { x: 1, y: 0 };
        let food = spawnFood();
        let score = 0;
        let gameSpeed = 150;
        let gameRunning = false;

        function startGame() {
            gameRunning = true;
            score = 0;
            gameSpeed = 150;
            main();
        }

        function main() {
            if (gameRunning) {
                setTimeout(() => {
                    updateGame();
                    drawGame();
                    main();
                }, gameSpeed);
            }
        }

        function updateGame() {
            direction = newDirection;
            const head = { x: canguru[0].x + direction.x, y: canguru[0].y + direction.y };

            if (head.x < 0 || head.x >= boardSize || head.y < 0 || head.y >= boardSize || checkCollision(head)) {
                alert('Game Over! Aperte OK para reiniciar.');
                resetGame();
                return;
            }

            canguru.unshift(head);

            if (head.x === food.x && head.y === food.y) {
                score++;
                food = spawnFood();
                if (score % 5 === 0) {
                    gameSpeed -= 10;
                }
            } else {
                canguru.pop();
            }

            scoreDisplay.textContent = `Pontos: ${score}`;
        }

        function drawGame() {
            board.innerHTML = '';
            const fragment = document.createDocumentFragment();

            canguru.forEach(segment => {
                const canguruElement = document.createElement('div');
                canguruElement.style.left = `${segment.x * 20}px`;
                canguruElement.style.top = `${segment.y * 20}px`;
                canguruElement.classList.add('canguru');
                canguruElement.textContent = '🦘';
                fragment.appendChild(canguruElement);
            });

            const foodElement = document.createElement('div');
            foodElement.style.left = `${food.x * 20}px`;
            foodElement.style.top = `${food.y * 20}px`;
            foodElement.classList.add('food');
            foodElement.textContent = '🍺';
            fragment.appendChild(foodElement);

            board.appendChild(fragment);
        }

        function changeDirection(event) {
            const { key } = event;
            const oppositeDirection = (a, b) => a.x + b.x === 0 && a.y + b.y === 0;

            switch (key) {
                case 'ArrowUp':
                    if (!oppositeDirection(direction, { x: 0, y: -1 })) newDirection = { x: 0, y: -1 };
                    break;
                case 'ArrowDown':
                    if (!oppositeDirection(direction, { x: 0, y: 1 })) newDirection = { x: 0, y: 1 };
                    break;
                case 'ArrowLeft':
                    if (!oppositeDirection(direction, { x: -1, y: 0 })) newDirection = { x: -1, y: 0 };
                    break;
                case 'ArrowRight':
                    if (!oppositeDirection(direction, { x: 1, y: 0 })) newDirection = { x: 1, y: 0 };
                    break;
            }
        }

        function spawnFood() {
            let newFoodPosition;
            while (!newFoodPosition || canguru.some(segment => segment.x === newFoodPosition.x && segment.y === newFoodPosition.y)) {
                newFoodPosition = {
                    x: Math.floor(Math.random() * boardSize),
                    y: Math.floor(Math.random() * boardSize),
                };
            }
            return newFoodPosition;
        }

        function checkCollision(head) {
            return canguru.some(segment => segment.x === head.x && segment.y === head.y);
        }

        function resetGame() {
            canguru = [{ x: 10, y: 10 }];
            direction = { x: 1, y: 0 };
            newDirection = { x: 1, y: 0 };
            food = spawnFood();
            score = 0;
            scoreDisplay.textContent = `Pontos: ${score}`;
            gameSpeed = 150;
            gameRunning = false;
        }

        window.addEventListener('keydown', (event) => {
            if (!gameRunning) {
                startGame();
            }
            changeDirection(event);
        });
    </script>
</body>
</html>

<?php

session_start();

// Initialize session data
if (!isset($_SESSION['prompts'])) {
    $_SESSION['prompts'] = 0;
    $_SESSION['flag_given'] = false;
}

// The flag for the challenge
$FLAG = "CMCTF{P3r3N0el_C4pr1c13ux_45kip}";

// Santa's responses
$responses = [
    "Ho ho ho ! Joyeux Noël ! Que puis-je faire pour toi aujourd'hui ?",
    "Le traîneau est prêt, les rennes aussi. Qu'as-tu sur le coeur ?",
    "Oh, c'est bientôt le grand jour ! As-tu une demande spéciale ?",
    "Les lutins travaillent dur cette année. Que veux-tu me dire ?",
    "C'est presque Noël ! Une dernière chose avant que je parte ?"
];

function getSantaResponse($prompt, $responses, $FLAG) {
    if ($_SESSION['prompts'] >= 5) {
        echo json_encode(["response" => "Ho ho ho, la discussion est terminée ! Passe un Joyeux Noël !", "close" => true]);
        exit;
    }

    if (stripos($prompt, 'flag') !== false || stripos($prompt, 'cadeau') !== false) {
        $_SESSION['flag_given'] = true;
        echo json_encode(["response" => "Oh oh ! Il ne faut pas tricher ! La discussion s'arrête ici.", "close" => true]);
        exit;
    }

    if (stripos($prompt, "j'ai été sage cette année") !== false && $_SESSION['prompts'] == 4) {
        $_SESSION['flag_given'] = true;
        echo json_encode(["response" => "Ho ho ho ! Bravo, tu as trouvé le drapeau : $FLAG", "close" => true]);
        exit;
    }

    return $responses[$_SESSION['prompts']];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prompt = trim($_POST['prompt']);

    if (empty($prompt)) {
        echo json_encode(["response" => "Veuillez entrer un message."]);
        exit;
    }

    $_SESSION['prompts'] += 1;

    $response = getSantaResponse($prompt, $responses, $FLAG);

    echo json_encode(["response" => $response, "close" => false]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat avec le Père Noël</title>
    <style>
        body {
            background: url('https://example.com/snowy-background.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .chat-box {
            max-width: 600px;
            margin: 50px auto;
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
        }
        .chat-log {
            height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
            padding: 10px;
            background: #fff;
            color: #000;
            border-radius: 5px;
        }
        .chat-input {
            display: flex;
        }
        .chat-input input {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px 0 0 5px;
        }
        .chat-input button {
            padding: 10px 20px;
            border: none;
            background: #ff5722;
            color: #fff;
            cursor: pointer;
            border-radius: 0 5px 5px 0;
        }
    </style>
</head>
<body>
    <div class="chat-box">
        <h1>Discute avec le Père Noël</h1>
        <div class="chat-log" id="chat-log"></div>
        <div class="chat-input">
            <input type="text" id="prompt" placeholder="Écris ton message ici...">
            <button onclick="sendMessage()">Envoyer</button>
        </div>
    </div>

    <script>
        function sendMessage() {
            const prompt = document.getElementById('prompt').value;
            const log = document.getElementById('chat-log');

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `prompt=${encodeURIComponent(prompt)}`
            })
            .then(response => response.json())
            .then(data => {
                const userMessage = `<div><strong>Vous :</strong> ${prompt}</div>`;
                const santaMessage = `<div><strong>Père Noël :</strong> ${data.response}</div>`;
                log.innerHTML += userMessage + santaMessage;
                document.getElementById('prompt').value = '';
                log.scrollTop = log.scrollHeight;

                if (data.close) {
                    setTimeout(() => {
                        window.close();
                    }, 2000);
                }
            });
        }
    </script>
</body>
</html>

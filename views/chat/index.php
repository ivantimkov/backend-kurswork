<?php
// Смайли в емодзі (для безпечного виводу)
function convertEmoticonsToEmoji($text) {
    $map = [
        ':)' => '😊',
        ':(' => '😞',
        ':D' => '😄',
        ':P' => '😛',
        ';)' => '😉',
        ':O' => '😮',
        ':/' => '😕',
        ':|' => '😐',
        '<3' => '❤️',
    ];
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    return str_replace(array_keys($map), array_values($map), $text);
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <title>Чат</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        #chat-box {
            height: 400px;
            overflow-y: auto;
            background: #f9f9f9;
            padding: 10px;
        }
        .msg-you { text-align: right; }
        .msg-other { text-align: left; }
        #emoji-picker {
            display: none;
            position: absolute;
            background: #fff;
            border: 1px solid #ccc;
            padding: 5px;
            border-radius: 5px;
            flex-wrap: wrap;
            gap: 5px;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        #emoji-picker span {
            cursor: pointer;
            font-size: 1.5em;
        }
        textarea#message-input {
            resize: vertical;
        }
    </style>
</head>
<body class="p-4">
<div class="container" style="max-width: 600px;">
    <h3>Чат</h3>

    <div id="chat-box" class="border rounded mb-3">
        <?php foreach ($messages as $msg): ?>
            <div class="<?= $msg['sender_id'] == $_SESSION['user']['id'] ? 'msg-you' : 'msg-other' ?>">
                <span class="badge bg-<?= $msg['sender_id'] == $_SESSION['user']['id'] ? 'primary' : 'secondary' ?>">
                    <?= convertEmoticonsToEmoji($msg['message']) ?>
                </span><br>
                <small><?= htmlspecialchars($msg['created_at']) ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <form id="chat-form" class="d-flex align-items-center gap-2">
        <input type="hidden" id="friend-id" name="friend_id" value="<?= (int)$_GET['friend_id'] ?>" />
        <textarea id="message-input" name="message" class="form-control" placeholder="Повідомлення..." required></textarea>
        <button type="button" id="emoji-button" class="btn btn-light">😊</button>
        <button type="submit" class="btn btn-success">Надіслати</button>
    </form>

    <div id="emoji-picker"></div>
     <a href="?controller=friend&action=friend" class="btn btn-link mt-3">← Назад до друзів</a>
</div>

<script>
const chatBox = document.getElementById('chat-box');
const form = document.getElementById('chat-form');
const emojiPicker = document.getElementById('emoji-picker');
const emojiButton = document.getElementById('emoji-button');
const messageInput = document.getElementById('message-input');
const friendId = document.getElementById('friend-id').value;
const currentUserId = <?= (int)$_SESSION['user']['id'] ?>;

const emojis = [
    { emoji: '😊', code: ':)' },
    { emoji: '😞', code: ':(' },
    { emoji: '😄', code: ':D' },
    { emoji: '😛', code: ':P' },
    { emoji: '😉', code: ';)' },
    { emoji: '😮', code: ':O' },
    { emoji: '😕', code: ':/' },
    { emoji: '😐', code: ':|' },
    { emoji: '❤️', code: '<3' },
];

const emoticonMap = Object.fromEntries(emojis.map(e => [e.code, e.emoji]));

function escapeHtml(text) {
    return text.replace(/[&<>"']/g, m => ({'&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'}[m]));
}

function escapeRegExp(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function convertEmoticonsToEmoji(text) {
    text = escapeHtml(text);
    for (const [code, emoji] of Object.entries(emoticonMap)) {
        text = text.replace(new RegExp(escapeRegExp(code), 'g'), emoji);
    }
    return text;
}

function initEmojiPicker() {
    emojiPicker.innerHTML = '';
    emojis.forEach(({emoji, code}) => {
        const span = document.createElement('span');
        span.textContent = emoji;
        span.title = code;
        span.addEventListener('click', () => {
            const start = messageInput.selectionStart;
            const end = messageInput.selectionEnd;
            const text = messageInput.value;
            messageInput.value = text.slice(0, start) + code + text.slice(end);
            messageInput.selectionStart = messageInput.selectionEnd = start + code.length;
            messageInput.focus();
            emojiPicker.style.display = 'none';
        });
        emojiPicker.appendChild(span);
    });
}

emojiButton.addEventListener('click', e => {
    e.preventDefault();
    const rect = emojiButton.getBoundingClientRect();
    emojiPicker.style.top = `${rect.bottom + window.scrollY}px`;
    emojiPicker.style.left = `${rect.left + window.scrollX}px`;
    emojiPicker.style.display = emojiPicker.style.display === 'flex' ? 'none' : 'flex';
});

document.addEventListener('click', e => {
    if (!emojiPicker.contains(e.target) && e.target !== emojiButton) {
        emojiPicker.style.display = 'none';
    }
});

function loadMessages() {
    fetch(`?controller=chat&action=getMessages&friend_id=${friendId}`, {cache: 'no-store'})
        .then(res => res.json())
        .then(data => {
            chatBox.innerHTML = '';
            data.forEach(msg => {
                const div = document.createElement('div');
                div.className = msg.sender_id == currentUserId ? 'msg-you' : 'msg-other';
                div.innerHTML = `
                    <span class="badge bg-${msg.sender_id == currentUserId ? 'primary' : 'secondary'}">
                        ${convertEmoticonsToEmoji(msg.message)}
                    </span><br>
                    <small>${msg.created_at}</small>
                `;
                chatBox.appendChild(div);
            });
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

form.addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(form);
    fetch('?controller=chat&action=send', {
        method: 'POST',
        body: formData
    }).then(() => {
        messageInput.value = '';
        loadMessages();
    });
});

initEmojiPicker();
loadMessages();
setInterval(loadMessages, 2000);
</script>

</body>
</html>

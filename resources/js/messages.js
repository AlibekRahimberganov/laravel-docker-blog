import axios from 'axios';

function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = `${textarea.scrollHeight}px`;
}

function scrollToBottom(list) {
    list.scrollTop = list.scrollHeight;
}

function initMessageForm() {
    const form = document.getElementById('message-form');
    if (!form) return;

    const input = document.getElementById('message-input');
    const sendButton = document.getElementById('message-send');
    const list = document.getElementById('message-list');

    scrollToBottom(list);
    autoResize(input);

    input.addEventListener('input', () => autoResize(input));

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            form.requestSubmit();
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const body = input.value.trim();
        if (!body || sendButton.disabled) return;

        sendButton.disabled = true;

        try {
            const { data } = await axios.post(
                form.action,
                { body },
                { headers: { Accept: 'application/json' } }
            );

            const empty = list.querySelector('p.text-gray-400');
            if (empty) empty.remove();

            list.insertAdjacentHTML('beforeend', data.html);
            scrollToBottom(list);

            input.value = '';
            autoResize(input);
            input.focus();
        } catch (error) {
            console.error('Failed to send message', error);
        } finally {
            sendButton.disabled = false;
        }
    });
}

document.addEventListener('DOMContentLoaded', initMessageForm);

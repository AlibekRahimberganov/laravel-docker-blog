import axios from 'axios';

const ACTIVE_CLASS = {
    like: 'text-blue-600',
    dislike: 'text-red-600',
    recommend: 'text-yellow-500',
};

const COUNT_KEY = {
    like: 'likes_count',
    dislike: 'dislikes_count',
    recommend: 'recommends_count',
};

function applyState(bar, data) {
    bar.querySelectorAll('.reaction-btn').forEach((btn) => {
        const type = btn.dataset.type;
        const isActive = Boolean(data.active[type]);

        const countEl = btn.querySelector('.reaction-count');
        if (countEl && data.counts[COUNT_KEY[type]] !== undefined) {
            countEl.textContent = data.counts[COUNT_KEY[type]];
        }

        btn.classList.toggle(ACTIVE_CLASS[type], isActive);
        btn.classList.toggle('text-gray-500', !isActive);
    });
}

function initReactions() {
    document.querySelectorAll('.reaction-btn').forEach((button) => {
        button.addEventListener('click', async () => {
            if (button.disabled) return;

            const bar = button.closest('[data-post-id]');
            button.disabled = true;

            try {
                const { data } = await axios.post(
                    button.dataset.url,
                    {},
                    { headers: { Accept: 'application/json' } }
                );

                if (bar) {
                    applyState(bar, data);
                }
            } catch (error) {
                console.error('Reaction failed', error);
            } finally {
                button.disabled = false;
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initReactions);

function initRecommendedToggle() {
    const button = document.getElementById('recommended-toggle');
    const more = document.getElementById('recommended-more');
    if (!button || !more) return;

    button.addEventListener('click', () => {
        const isHidden = more.style.display === 'none';
        more.style.display = isHidden ? 'flex' : 'none';
        button.textContent = isHidden ? 'Show less' : 'Show more';
    });
}

document.addEventListener('DOMContentLoaded', initRecommendedToggle);

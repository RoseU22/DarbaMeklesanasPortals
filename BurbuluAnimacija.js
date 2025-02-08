document.addEventListener("DOMContentLoaded", () => {
    const bubbleContainer = document.querySelector(".burbulu-background");

    function createBubble() {
        const bubble = document.createElement("div");
        bubble.classList.add("burbulis");

        // Random size
        const size = Math.random() * 40 + 10; // Between 10px and 50px
        bubble.style.width = `${size}px`;
        bubble.style.height = `${size}px`;

        // Random position
        bubble.style.left = `${Math.random() * 100}%`;

        // Random animation duration
        const duration = Math.random() * 5 + 3; // Between 3s and 8s
        bubble.style.animationDuration = `${duration}s`;

        bubbleContainer.appendChild(bubble);

        // Remove bubble after animation ends
        setTimeout(() => {
            bubble.remove();
        }, duration * 1000);
    }

    // Generate bubbles every 500ms
    setInterval(createBubble, 500);
});

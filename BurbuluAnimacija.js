document.addEventListener("DOMContentLoaded", () => {
    const bubbleContainer = document.querySelector(".burbulu-background");

    function createBubble() {
        const bubble = document.createElement("div");
        bubble.classList.add("burbulis");


        const size = Math.random() * 40 + 10; 
        bubble.style.width = `${size}px`;
        bubble.style.height = `${size}px`;


        bubble.style.left = `${Math.random() * 100}%`;

        
        const duration = Math.random() * 5 + 3;
        bubble.style.animationDuration = `${duration}s`;

        bubbleContainer.appendChild(bubble);


        setTimeout(() => {
            bubble.remove();
        }, duration * 1000);
    }

    setInterval(createBubble, 500);
});

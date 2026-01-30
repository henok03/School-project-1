const track = document.getElementById('track');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const cards = document.querySelectorAll('.testimonial-card');
        
        let currentIndex = 0;

        function updateSlider() {
            // Check how many cards are currently visible based on screen size
            let visibleCards = 3;
            if (window.innerWidth <= 1024) visibleCards = 2;
            if (window.innerWidth <= 640) visibleCards = 1;

            const maxIndex = cards.length - visibleCards;

            // Constrain index
            if (currentIndex > maxIndex) currentIndex = 0;
            if (currentIndex < 0) currentIndex = maxIndex;

            // Calculate movement
            const gap = 24;
            const cardWidth = cards[0].offsetWidth;
            const moveAmount = currentIndex * (cardWidth + gap);

            track.style.transform = `translateX(-${moveAmount}px)`;
        }

        nextBtn.addEventListener('click', () => {
            currentIndex++;
            updateSlider();
        });

        prevBtn.addEventListener('click', () => {
            currentIndex--;
            updateSlider();
        });

        // Recalculate on resize to fix positioning
        window.addEventListener('resize', () => {
            currentIndex = 0; // Reset to avoid layout bugs
            updateSlider();
        });
        
        // Final call to ensure correct init
        updateSlider();
        

    
        const rail = document.querySelector('#mainRail');
        const pillBox = document.querySelector('#pillBox');
        const items = document.querySelectorAll('.gallery-item');
        const btnLeft = document.querySelector('#leftArrow');
        const btnRight = document.querySelector('#rightArrow');

        let position = 0;

        function checkCapacity() {
            if (window.innerWidth <= 640) return 1;
            if (window.innerWidth <= 1024) return 2;
            return 3;
        }

        function buildPills() {
            pillBox.innerHTML = '';
            const totalSteps = items.length - (checkCapacity() - 1);
            for (let i = 0; i < totalSteps; i++) {
                const pill = document.createElement('button');
                pill.className = 'step-pill' + (i === 0 ? ' is-active' : '');
                pill.addEventListener('click', () => jumpTo(i));
                pillBox.appendChild(pill);
            }
        }

        function refreshUI() {
            const pills = document.querySelectorAll('.step-pill');
            pills.forEach((p, idx) => {
                p.classList.toggle('is-active', idx === position);
            });

            const space = 30; // Gap size
            const itemWidth = items[0].offsetWidth;
            const scrollDist = position * (itemWidth + space);
            rail.style.transform = `translateX(-${scrollDist}px)`;
        }

        function jumpTo(target) {
            position = target;
            refreshUI();
        }

        btnRight.addEventListener('click', () => {
            const limit = items.length - checkCapacity();
            position = (position < limit) ? position + 1 : 0;
            refreshUI();
        });

        btnLeft.addEventListener('click', () => {
            const limit = items.length - checkCapacity();
            position = (position > 0) ? position - 1 : limit;
            refreshUI();
        });

        window.addEventListener('resize', () => {
            position = 0;
            buildPills();
            refreshUI();
        });

        // Initialize
        buildPills();
       
        // JavaScript to detect scroll and toggle the 'scrolled' class
       // Trigger the color change after scrolling 60px
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 60) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
      
    
  



<svg data-aos="fade-right"
     data-aos-offset="300"
     data-aos-easing="ease-in-sine" class="wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
    <path fill="currentColor" fill-opacity="1"
          d="M0,128L17.1,133.3C34.3,139,69,149,103,176C137.1,203,171,245,206,261.3C240,277,274,267,309,245.3C342.9,224,377,192,411,197.3C445.7,203,480,245,514,245.3C548.6,245,583,203,617,160C651.4,117,686,75,720,64C754.3,53,789,75,823,106.7C857.1,139,891,181,926,192C960,203,994,181,1029,170.7C1062.9,160,1097,160,1131,170.7C1165.7,181,1200,203,1234,192C1268.6,181,1303,139,1337,101.3C1371.4,64,1406,32,1423,16L1440,0L1440,320L1422.9,320C1405.7,320,1371,320,1337,320C1302.9,320,1269,320,1234,320C1200,320,1166,320,1131,320C1097.1,320,1063,320,1029,320C994.3,320,960,320,926,320C891.4,320,857,320,823,320C788.6,320,754,320,720,320C685.7,320,651,320,617,320C582.9,320,549,320,514,320C480,320,446,320,411,320C377.1,320,343,320,309,320C274.3,320,240,320,206,320C171.4,320,137,320,103,320C68.6,320,34,320,17,320L0,320Z"></path>
</svg>

<main class="container">
    <section id="introduction" class="grid">
        <div>
            <hgroup>
                <h1>Steamy Sips Café</h1>
                <h2>Where Every Cup Tells a Story</h2>
            </hgroup>
            <p>Welcome to Steamy Sips Café, where every sip is an experience. Step into our cozy world of aromatic
                delights, where the perfect brew meets community and conversation. </p>
            <div>
                <a href="<?= ROOT ?>/shop" role="button" class="contrast">Shop now</a>
            </div>
        </div>
        <div>
            <img src="<?= ROOT ?>/assets/img/home-coffee.png"
                 height="200px" width="500px"
                 alt="Coffee beans in a white bowl">
        </div>
    </section>

    <section data-aos="fade-left" id="about-us">
        <h2>About Us</h2>
        <div class="grid">
            <article>
                <header>
                    <h5 data-theme="dark">Craft Your Blend</h5>
                </header>
                <small>Personalize your coffee blend, selecting from diverse beans, roasts, and flavors for a truly
                    unique brew tailored to your preferences.</small>
            </article>
            <article>
                <header>
                    <h5 data-theme="dark">Tasting Experiences</h5>
                </header>
                <small>Join our guided tasting sessions exploring various coffee origins, roasting methods, and flavor
                    profiles, elevating your understanding and enjoyment of coffee.</small>
            </article>
            <article>
                <header>
                    <h5 data-theme="dark">Ethical Sourcing</h5>
                </header>
                <small>Embrace our commitment to ethical bean sourcing and sustainable practices, supporting fair trade,
                    eco-friendly packaging, and recycling initiatives.</small>
            </article>
        </div>
    </section>

    <section data-aos="fade-right" id="testimonials" class="splide" aria-label="Beautiful Images">
        <div class="con">
            <h2>What Our Customers Say</h2>
        </div>
        <div class="splide__track">
            <ul class="splide__list">
                <li class="splide__slide">
                    <article>
                        <header>
                            <img class="avatar" src="<?= ROOT ?>/assets/img/testimonial-bill-gates.jpg"
                                 alt="">
                            <hgroup>
                                <h5>Bill Gates</h5>
                                <h6>Microsoft CEO</h6>
                            </hgroup>
                        </header>
                        <p><em>I have been a customer of steamy sips for a few months now and I have to say, I am
                                extremely impressed with the quality and freshness of their coffee. The selection is
                                also
                                fantastic, I love trying new blends and flavors. Highly recommend steamy sips.</em></p>
                    </article>
                </li>
                <li class="splide__slide">

                    <article>
                        <header>
                            <img class="avatar" src="<?= ROOT ?>/assets/img/testimonial-john-brookes.jpg"
                                 alt="White woman with black beanie">
                            <hgroup>
                                <h5>John Brookes</h5>
                                <h6>Facebook CEO</h6>
                            </hgroup>
                        </header>
                        <p><em>As a coffee connoisseur, I have tried beans from many different roasters, but none
                                compare to the quality and taste of the beans from steamy sips. Their commitment to
                                sustainability and ethical sourcing is also a huge plus. I will definitely be a
                                long-time customer.</em></p>
                    </article>
                </li>
                <li class="splide__slide">
                    <article>
                        <header>
                            <img class="avatar" src="<?= ROOT ?>/assets/img/testimonial-sarah-clay.jpg"
                                 alt="White woman with black beanie">
                            <hgroup>
                                <h5>Sarah Clay</h5>
                                <h6>Apple HR</h6>
                            </hgroup>
                        </header>
                        <p><em>I have been a loyal customer of Steamy Sips for over a year now and I have never been
                                disappointed. Their beans are always fresh and delicious, and the customer service is
                                top-notch. I love supporting a business that is committed to sustainability and ethical
                                sourcing.</em></p>
                    </article>
                </li>
            </ul>
        </div>
    </section>

    <section data-aos="fade-left" id="closing-argument">
        <article>
            <h2>Experience the Difference</h2>
            <p>Join us at Steamy Sips Café, where every moment is steeped in passion and flavor...</p>
            <button class="outline">Subscribe to our newsletter</button>
            <!-- Your closing argument content -->
        </article>
    </section>
</main>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    AOS.init();

    new Splide("#testimonials", {
      perPage: 2,
      breakpoints: {
        1000: {
          perPage: 1,
        },
      },
      lazyLoad: "nearby",
      preloadPages: 3,
      focus: 0,
    }).mount();
  });
</script>
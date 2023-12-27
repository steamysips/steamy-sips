<main class="container">
    <article class="grid" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="500">
        <div>
            <h1>Sign in</h1>
            <form>
                <input type="text" name="login" placeholder="Login" aria-label="Login" autocomplete="nickname" required />
                <input type="password" name="password" placeholder="Password" aria-label="Password" autocomplete="current-password" required />
                <fieldset>
                    <label for="remember">
                        <input type="checkbox" role="switch" id="remember" name="remember" />
                        Remember me
                    </label>
                </fieldset>
                <button type="submit" class="contrast">Login</button>
                <small>Don't have an account yet? <a href="<?= ROOT ?>/register">Register</a></small>
            </form>
        </div>
        <div></div>
    </article>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init();
    });
</script>
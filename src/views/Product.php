<main class="container">
    <div class="grid">
        <img src="<?= ROOT ?>/assets/img/login-milkshake.avif" alt="">
        <div class="">
            <hgroup>
                <h2>Espresso</h2>
                <h3>Average rating: 3.1</h3>
            </hgroup>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
                dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
                deserunt mollit anim id est laborum.
            </p>
            <button>Add to cart</button>
        </div>
    </div>

    <h2>Customer Reviews</h2>
    <form action="" class="grid">
        <label>
            <input placeholder="Write a new review" type="text">
        </label>
        <button type="submit">Submit</button>
    </form>
    <div id="card_01_comments">


    </div>
</main>

<style>
  ul > li {
    list-style-type: none;
  }

  li > article {
    margin: 0;
  }

  ul > ul {
    padding-left: 55px;
  }
</style>


<script>
  // ! JAVASCRIPT IS NOT NEEDED
  // THIS CAN BE DONE IN PHP on server-side
  const comments = [
    {
      text: "this is the first comment",
      children: [
        {
          text: "this is the first subcomment",
          children: "",
        },
        {
          text: "this is the second subcomment",
          children: [
            {
              text: "third level comment",
              children: [{text: "fourth level"}],
            },
          ],
        },
      ],
    },
    {text: "this is the second comment"},
  ];

  let commentary = "<ul>";

  function getCommentHTML() {

  }

  function recurse(comment) {
    commentary += `<li><article>${comment.text}</article></li>`;
    if (comment.children) {
      commentary += "<ul>";
      for (i in comment.children) {
        recurse(comment.children[i]);
      }
      commentary += "</ul>";
    }
  }

  for (comment in comments) {
    recurse(comments[comment]);
  }

  document.querySelector("#card_01_comments").innerHTML += commentary + "</ul>";
</script>
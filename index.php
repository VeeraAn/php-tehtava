<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Sähköpostin tallennus</title>
  </head>

  <body>
    <div class="container">
        <div class="row">
            <div class="mt-3 col-lg-8">
                <h3 class="mt-1 mb-3">Jätä sähköpostiosoitteesi</h3>

                <form action="<?php print($_SERVER['PHP_SELF']); ?>" method="post">
                    <div class="form-group">                                
                        <label>Sähköpostiosoite: </label>
                        <input class="form-control" type="email" name="email" maxlength="100" required>
                    </div>
                    <div class="form-group">                            
                        <button type="submit" class="btn btn-primary">Lähetä</button>
                    </div>   
                </form>
            </div>
        </div>
    
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $domain = separateDomain($email);
    $db = new PDO('mysql:host=localhost;dbname=webbilomake;charset=utf8', 'root', '');
    
    if (!emailExists($email, $db)) {
        if (checkdnsrr($domain, "MX")) { //Check if domain is really existing
            try {
                $sql = "INSERT into info (email) values "
                    . "(:email)";
                $statement = $db->prepare($sql);
                $statement->bindValue(':email', $email, PDO::PARAM_STR);

                if ($statement->execute()) {
                    print "<p class='badge badge-info'>Sähköpostiosoite tallennettu!</p>";
                } else {
                    print("<p class='badge badge-warning'>Virhe tallennuksessa, kokeile uudelleen.</p>");
                }
            } catch (Exception $ex) {
                print "<p class='badge badge-danger'>Virhe tietokantayhteydessä: " . $ex->getMessage() . "</p>";
            }
        } else {
            print("<p class='badge badge-warning'>Virheellinen sähköpostiosoite.</p>");
        }
    } else {
        print("<p class='badge badge-warning'>Sähköpostiosoite on jo käytössä</p>");
    }
}

    //Take domain from email for checking if domain exists
    function separateDomain($email){
        $domain = substr(strrchr($email, "@"), 1);
        return $domain;
    }

    //Check if email is already in database
    function emailExists($email, $db){
        $statement = $db->prepare("SELECT * FROM info WHERE email=?");
        $statement->execute([$email]);
        $info = $statement->fetch();
        if ($info) {
            return true;
        } else {
            return false;
        }
    }

?>
    </div> 
  </body>
</html>
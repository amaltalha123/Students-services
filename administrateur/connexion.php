<?php

session_start();


$servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "services";
    $conn = new mysqli($servername, $username_db, $password_db, $dbname, 3306);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : '';
    $password = isset($_POST['password']) ? trim(htmlspecialchars($_POST['password'])) : '';

    

    $sql = "SELECT * FROM administrateurs WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $stored_password = $user_data['mot_de_passe'];

   
        if (password_verify($password, $stored_password) || $password === $stored_password) {
           
            $_SESSION['user_id'] = $user_data['id_admin'];
            $_SESSION['username'] = $email;
            $_SESSION['admin_name'] = $user_data['nom'];
          
            if ($password === $stored_password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE administrateurs SET mot_de_passe = ? WHERE email = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param('ss', $hashed_password, $email);
                $update_stmt->execute();
            }

            echo json_encode(["status" => "success", "redirect" => "admindashboard.php"]);
            exit();
           
        } 
    }else{
        echo json_encode(["status" => "error", "message" => "Mot de passe incorrect"]);
    }

    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,
initial-scale=1.0">
<title>Login</title>
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap");

* {
margin: 0;
padding: 0;
box-sizing: border-box;
font-family: "Poppins", sans-serif;
}

body {
display: flex;
justify-content: center;
align-items: center;
min-height: 100vh;
background:url(pngtree-illustration-of-a-flying-graduation-cap-in-3d-render-celebratory-banner-image_3634765.jpg) no-repeat;
background-size: cover;
background-position: center;
}

.wrapper {
width: 420px;
background:transparent;
border: 2px solid rgba(255, 255, 255, .2);
backdrop-filter: blur(20px);
box-shadow: 0 0 10px rgba(0, 0, 0, .2);
color: white;
border-radius: 10px;
padding: 30px 40px;
overflow: hidden;
}


.wrapper h1 {
    font-size: 36px;
    text-align: center;
}

.wrapper .input-box {
    position: relative;
    width: 100%;
    height: 50px;
    margin: 30px 0;
}

.input-box input {
    width: 100%;
    height: 100%;
    background: transparent;
    border: none;
    outline: none;
    border: 2px solid rgba(255, 255, 255, .2);
    border-radius: 40px;
    font-size: 16px;
    color: #fff;
    padding: 20px 45px 20px 20px;
}



.input-box input::placeholder {
color: #fff;
}

.input-box i {
position: absolute;
right: 20px;
top: 50%;
transform: translateY(-50%);
font-size: 20px;
}

.wrapper .remember-forgot {
    display: flex;
    justify-content: space-between;
    font-size: 14.5px;
    margin: -15px 0 15px;
}
    
    
.remember-forgot label input {
    accent-color: #fff;
    margin-right: 3px;
}

.remember-forgot a {
    color: #fff;
    text-decoration: none;
}

.remember-forgot a:hover {
    text-decoration: underline;
}

    .wrapper .btn {
    width: 100%;
    height: 45px;
    background: #fff;
    border: none;
    outline: none;
    border-radius: 40px;
    border-radius: 40px;
    box-shadow: 0 0 10px rgba(0, 0, 0, .1);
    cursor: pointer;
    font-size: 16px;
    color: #333;
    font-weight: 600;
}
    
.wrapper .register-link {
    font-size: 14.5px;
    text-align: center;
    margin: 20px 0 15px;
    
    }
    
    .register-link p a {
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    
    }
    
    .register-link p a:hover {
    text-decoration: underline;
    }

    .feedback {
    font-size: 0.9em;
    margin-top: 5px;
    display: block;
}

.input-box .invalide {
            border: 2px solid red;
        }
        .input-box.valide {
            border: 2px solid green;
        }
        #submitButton:disabled {
            background-color: gray;
            cursor: not-allowed;
        }
        span.error {
            color: red;
            font-size: 12px;
        }
        .reponse{
           color:#fff; 
        }

   
</style>
</head>

<body>
    

    <div class="wrapper">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" >
            <h1>Login</h1>
            <div class="input-box">
                <input type="text" name="email" placeholder="email" id="email" required>
                <i class='bx bxs-envelope'></i>
                <span id="email_error" class="error"></span>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="mot de passe" id="password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <button type="submit" class="btn" id="submitButton" disabled>Connexion</button>
        </form>
        <div id="response"></div>
    </div>
    <script src="scriptconnexion.js">
</script>

</body>
</html>

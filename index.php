<?php
require('config/conexao.php');

if(isset($_POST['email']) && isset($_POST['senha']) && !empty($_POST['email']) && !empty($_POST['senha'])){
    //RECEBER OS DADOS VINDOS DO POST E LIMPAR
    $email = limparPost($_POST['email']);
    $senha = limparPost($_POST['senha']);
    $senha_cript = sha1($senha);

    //VERIFICAR SE EXISTE ESTE USUÁRIO
    $sql = $pdo->prepare("SELECT * FROM usuarios WHERE email=? AND senha=? LIMIT 1");
    $sql->execute(array($email,$senha_cript));
    $usuario = $sql->fetch(PDO::FETCH_ASSOC);
    if($usuario){
        //EXISTE O USUÁRIO
        //VERIFICAR SE O CADASTRO FOI CONFIRMADO
        if($usuario['status']=="confirmado"){
            //CRIAR UM TOKEN
            $token = sha1(uniqid().date('d-m-Y-H-i-s'));

            //ATUALIZAR O TOKEN DESTE USUÁRIO NO BANCO
            $sql = $pdo->prepare("UPDATE usuarios SET token=? WHERE email=?AND senha=?");
            if($sql->execute(array($token,$email,$senha_cript))){
                //ARMAZENAR ESTE TOKEN NA SESSÃO (SESSION)
                $_SESSION['TOKEN'] = $token;
                header('location: restrita.php');
            }
        }else{
            $erro_login = "Favor confirmar seu cadastro no e-mail.";
        }
        }
    }else{
        $erro_login = "Usuário e/ou senha incorretos!";
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/estilo.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
    />
    <title>Login</title>
</head>
<body>
    <form method="post">
        <h1>Login</h1>

        <?php if(isset($_GET['result']) && ($_GET['result']=="ok")){ ?>
            <div class="sucesso animate__animated animate__heartBeat">
            Cadastrado com suscesso
            </div>
        <?php } ?>

        <?php if(isset($erro_login)){ ?>
            <div style="text-aling:center" class="erro-geral animate__animated animate__rubberBand">
            <?php echo $erro_login; ?>
        </div>
        <?php } ?>

        <div class="input-group">
            <img class="input-icon" src="img/user.png">
            <input type="email" name="email" placeholder="Digite seu e-mail." required>
        </div>

        <div class="input-group">
            <img class="input-icon" src="img/lock.png">
            <input type="password" name="senha" placeholder="Digite sua senha." required>
        </div>
        
        <button type="submit" class="btn-blue">Fazer Login</button>
        <a href="cadastrar.php">Ainda não tenho cadastro</a>
    </form>
    
    <?php if(isset($_GET['result']) && ($_GET['result']=="ok")){ ?>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        setTimeout(() => {
           $('.sucesso').hide();         
        }, 3000);
    </script>
    <?php } ?>
    
</body>
</html>
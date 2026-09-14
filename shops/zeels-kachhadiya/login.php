<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/icons.php';
ensure_default_admin();
if (current_team_member()) { header('Location: index.php'); exit; }
$members=db_get_all('team'); $subadmins=subadmin_db_all(); $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $username=trim((string)($_POST['username']??'')); $password=(string)($_POST['password']??'');
  foreach($members as $member){ if(strcasecmp((string)($member['username']??''),$username)===0 && password_verify($password,(string)($member['password']??''))){
    $_SESSION['subadmin']=null; unset($_SESSION['subadmin']);
    $_SESSION['team_member']=['id'=>$member['id'],'name'=>$member['name'],'username'=>$member['username'],'role'=>$member['role']??'Member','photo'=>$member['photo']??''];
    header('Location:index.php'); exit;
  }}
  foreach($subadmins as $sa){ if((string)($sa['status']??'Active')==='Active' && strcasecmp((string)($sa['username']??''),$username)===0 && password_verify($password,(string)($sa['password']??''))){
    unset($_SESSION['team_member']);
    $_SESSION['subadmin']=['id'=>$sa['id'],'name'=>$sa['name'],'username'=>$sa['username'],'role'=>'Subadmin','phone'=>$sa['phone']??'','photo'=>''];
    header('Location:index.php'); exit;
  }}
  $error='Invalid username or password.';
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Login — Prisha Ayurvedic ERP</title><link rel="stylesheet" href="assets/style.css"></head><body class="login-page"><main class="login-card"><div class="login-brand"><img src="assets/logo.png" alt="Prisha Ayurvedic logo"></div><h1>ERP Login</h1><p class="login-subtitle">Sign in to manage your workspace.</p><?php if($error):?><div class="alert alert-error"><?=htmlspecialchars($error)?></div><?php endif;?><form method="post" class="login-form"><label>Username</label><input type="text" name="username" required autofocus autocomplete="username"><label>Password</label><div class="password-field"><input id="login-password" type="password" name="password" required autocomplete="current-password"><button type="button" class="password-toggle" data-target="login-password"><span class="password-icon-show"><?=icon('view',16)?></span><span class="password-icon-hide password-icon-hidden"><?=icon('eye-off',16)?></span></button></div><button class="btn btn-primary login-button" type="submit">Sign In</button></form></main><script>document.querySelectorAll('.password-toggle').forEach(b=>b.addEventListener('click',()=>{const i=document.getElementById(b.dataset.target);const show=i.type==='password';i.type=show?'text':'password';}));</script></body></html>

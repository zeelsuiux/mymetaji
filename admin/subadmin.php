<?php
require_once __DIR__ . '/auth.php'; require_admin(); require_once __DIR__ . '/icons.php';
$action=$_GET['action']??'list'; $id=(int)($_GET['id']??0); $errors=[];
if(in_array($action, ['new','edit','save','delete'], true)) require_active_license();
$rows=subadmin_db_all();
if($_SERVER['REQUEST_METHOD']==='POST' && $action==='save'){
  $id=(int)($_POST['id']??0); $name=trim((string)($_POST['name']??'')); $phone=trim((string)($_POST['phone']??'')); $username=trim((string)($_POST['username']??'')); $password=(string)($_POST['password']??'');
  if($name==='')$errors[]='Name is required.'; if($username==='')$errors[]='Username is required.';
  foreach($rows as $r) if((int)$r['id']!==$id && strcasecmp((string)($r['username']??''),$username)===0)$errors[]='Username already exists.';
  if($id===0 && $password==='')$errors[]='Password is required.';
  if(!$errors){
    if($id>0){foreach($rows as &$r)if((int)$r['id']===$id){$r['name']=$name;$r['phone']=$phone;$r['username']=$username;if($password!=='')$r['password']=password_hash($password,PASSWORD_DEFAULT);$r['status']=$r['status']??'Active';$r['updated_at']=date('Y-m-d H:i:s');break;}unset($r);}
    else {$rows[]=['id'=>subadmin_next_id($rows),'name'=>$name,'phone'=>$phone,'username'=>$username,'password'=>password_hash($password,PASSWORD_DEFAULT),'status'=>'Active','created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')];}
    subadmin_db_save($rows); header('Location: subadmin.php?ok=saved'); exit;
  }
  $form=['id'=>$id,'name'=>$name,'phone'=>$phone,'username'=>$username];
}elseif($action==='edit'){$form=db_get_subadmin($id);if(!$form){header('Location: subadmin.php');exit;}}
if($action==='delete' && $id){$rows=array_values(array_filter($rows,fn($r)=>(int)$r['id']!==$id));subadmin_db_save($rows);header('Location: subadmin.php?ok=deleted');exit;}
$pageTitle='Subadmin'; $activeModule='subadmin'; require __DIR__ . '/layout_top.php';
?>
<div class="panel"><div class="panel-header"><h2><?=icon('customers')?> Subadmin <span class="count-pill">(<?=count($rows)?>)</span></h2><div class="toolbar"><a class="btn btn-primary" href="subadmin.php?action=new"><?=icon('add',16)?> Add Subadmin</a></div></div><div class="panel-body">
<?php if(isset($_GET['ok'])):?><div class="alert">Record <?=htmlspecialchars($_GET['ok'])?> successfully.</div><?php endif;?>
<?php if($action==='new'||$action==='edit'): $form=$form??[]; ?><form method="post" action="subadmin.php?action=save"><input type="hidden" name="id" value="<?= (int)($form['id']??0) ?>"><div class="form-grid"><div class="form-group"><label>Name *</label><input name="name" required value="<?=htmlspecialchars($form['name']??'')?>"></div><div class="form-group"><label>Phone</label><input name="phone" value="<?=htmlspecialchars($form['phone']??'')?>"></div><div class="form-group"><label>Username *</label><input name="username" required value="<?=htmlspecialchars($form['username']??'')?>"></div><div class="form-group"><label>Password <?=($form?'(leave blank to keep current)':'*')?></label><input type="password" name="password" <?=empty($form)?'required':''?>></div></div><?php if($errors):?><div class="alert alert-error"><?=implode('<br>',array_map('htmlspecialchars',$errors))?></div><?php endif;?><div class="form-actions"><a class="btn btn-outline" href="subadmin.php">Back</a><button class="btn btn-primary" type="submit">Save Subadmin</button></div></form><?php else: ?>
<table class="data-table"><thead><tr><th>Name</th><th>Phone</th><th>Username</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?=htmlspecialchars($r['name']??'-')?></td><td><?=htmlspecialchars($r['phone']??'-')?></td><td><?=htmlspecialchars($r['username']??'-')?></td><td><span class="badge green"><?=htmlspecialchars($r['status']??'Active')?></span></td><td class="row-actions"><a class="btn btn-outline btn-sm" href="subadmin_activity.php?id=<?=$r['id']?>"><?=icon('calendar',14)?> View Activity</a><a class="btn btn-outline btn-sm" href="subadmin.php?action=edit&id=<?=$r['id']?>"><?=icon('edit',14)?> Edit</a><a class="btn btn-danger btn-sm" href="subadmin.php?action=delete&id=<?=$r['id']?>" onclick="return confirm('Delete this subadmin?')"><?=icon('delete',14)?></a></td></tr><?php endforeach;?></tbody></table><?php endif;?></div></div><?php require __DIR__ . '/layout_bottom.php'; ?>

{include file='header' pageTitle='wcf.acp.packageserver.package.generalpermission.add'}

<header class="boxHeadline">
	<hgroup>
		<h1>{lang}wcf.acp.packageserver.package.generalpermission.add{/lang}</h1>
	</hgroup>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.add{/lang}</p>
{/if}

<div class="contentNavigation">

</div>

<form method="post" action="{link controller='PackageGeneralPermissionAdd'}{/link}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.acp.packageserver.permission{/lang}</legend>

			<dl{if $errorField == 'package'} class="formError"{/if}>
				<dt><label for="package">{lang}wcf.acp.packageserver.package{/lang}</label></dt>
				<dd>
					<input type="text" id="package" name="package" value="{$package}" required="required" class="medium" />
					{if $errorField == 'package'}
						<small class="innerError">
							{lang}wcf.global.form.error.empty{/lang}
						</small>
					{/if}
				</dd>
			</dl>
				
			<dl{if $errorField == 'permission'} class="formError"{/if}>
				<dt><label for="permission">{lang}wcf.acp.packageserver.permission{/lang}</label></dt>
				<dd>
					<input type="text" id="permission" name="permission" value="{$permission}" required="required" class="medium" />
					{if $errorField == 'permission'}
						<small class="innerError">
							{lang}wcf.global.form.error.empty{/lang}
						</small>
					{/if}
				</dd>
			</dl>
	</fieldset>
</div>

<div class="formSubmit">
	<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
	{@SECURITY_TOKEN_INPUT_TAG}
</div>
</form>

{include file='footer'}
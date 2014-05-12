{include file='header' pageTitle='wcf.acp.packageserver.package.add'}

<header class="boxHeadline">
	<hgroup>
		<h1>{lang}wcf.acp.packageserver.package.add{/lang}</h1>
	</hgroup>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.add{/lang}</p>	
{/if}

<div class="contentNavigation">

</div>

<form enctype="multipart/form-data" method="post" action="{link controller='PackageAdd'}{/link}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.acp.packageserver.package{/lang}</legend>

			<dl{if $errorField == 'package'} class="formError"{/if}>
				<dt><label for="package">{lang}wcf.acp.packageserver.package{/lang}</label></dt>
				<dd>
					<input type="file" id="package" name="package" required="required" autofocus="autofocus" class="medium" />
					{if $errorField == 'package'}
						<small class="innerError">
							{lang}wcf.acp.packageserver.package.fail.{$errorType}{/lang}
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
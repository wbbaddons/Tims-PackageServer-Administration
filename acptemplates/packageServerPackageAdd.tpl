{include file='header' pageTitle='wcf.acp.packageserver.package.add'}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.packageserver.package.add{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.add{/lang}</p>
{/if}

<form enctype="multipart/form-data" method="post" action="{link controller='PackageServerPackageAdd'}{/link}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.acp.packageserver.package.upload{/lang}</legend>
			
			<dl{if $errorField == 'package'} class="formError"{/if}>
				<dt><label for="package">{lang}wcf.acp.packageserver.package.upload{/lang}</label></dt>
				<dd>
					<input type="file" id="package" name="package" required="required" autofocus="autofocus" class="long" />
					{if $errorField == 'package'}
						<small class="innerError">
							{lang}wcf.acp.packageserver.package.error.{$errorType}{/lang}
						</small>
					{/if}
				</dd>
			</dl>
		</fieldset>
	</div>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{csrfToken}
	</div>
</form>

{include file='footer'}

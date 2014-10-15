{include file='header' pageTitle='wcf.acp.packageserver.package.list'}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.packageserver.package.list{/lang}</h1>
</header>

<div class="contentNavigation">
	{hascontent}
		<nav>
			{content}
				{if $__wcf->session->getPermission('admin.packageServer.canManagePackages')}
					<ul>
						<li>
							<a href="{link controller='PackageServerPackageAdd'}{/link}" title="" class="button">
								<span class="icon icon16 icon-plus"></span>
								<span>{lang}wcf.acp.packageserver.package.add{/lang}</span>
							</a>
						</li>
					</ul>
				{/if}
				
				{event name='additonalNavigationLinks'}
			{/content}
		</nav>
	{/hascontent}
</div>

{hascontent}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}wcf.acp.packageserver.packages{/lang} <span class="badge badgeInverse">{#$versionCount}</span></h2>
		</header>
		
		<table class="table">
			<thead>
				<tr>
					<th class="columnTitle" colspan="2">{lang}wcf.acp.packageserver.package.identifier{/lang}</th>
					<th class="columnText">{lang}wcf.acp.packageserver.package.version{/lang}</th>
					<th class="columnText">{lang}wcf.acp.packageserver.package.downloads{/lang}</th>
					
					{event name='headColumns'}
				</tr>
			</thead>
			
			<tbody>
				{content}
					{foreach from=$items item=versions key=packageIdentifier}
						{foreach from=$versions key=version item=downloads}
							<tr>
								<td class="columnIcon">
									<a href="{link controller='PackageServerDeletePackageVersion' packageIdentifier=$packageIdentifier version=$version}{/link}" title="{lang}wcf.global.button.delete{/lang}" class="jsTooltip"><span class="icon icon16 icon-remove"></span></a>
								</td>
								<td class="columnTitle"><p>{$packageIdentifier}</p></td>
								<td class="columnText"><p>{$version}</p></td>
								<td class="columnDigits"><p>{$downloads}</p></td>
								
								{event name='columns'}
							</tr>
						{/foreach}
					{/foreach}
				{/content}
			</tbody>
		</table>
	</div>
{hascontentelse}
	<p class="info">{lang}wcf.acp.packageserver.package.noresults{/lang}</p>
{/hascontent}

<div class="contentNavigation">
	{hascontent}
		<nav>
			{content}
				{if $__wcf->session->getPermission('admin.packageServer.canManagePackages')}
					<ul>
						<li>
							<a href="{link controller='PackageServerPackageAdd'}{/link}" title="" class="button">
								<span class="icon icon16 icon-plus"></span>
								<span>{lang}wcf.acp.packageserver.package.add{/lang}</span>
							</a>
						</li>
					</ul>
				{/if}
				
				{event name='additonalNavigationLinks'}
			{/content}
		</nav>
	{/hascontent}
</div>

{include file='footer'}

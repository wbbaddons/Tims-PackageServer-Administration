{include file='header' pageTitle='admin.packageServer.package.list'}

<header class="boxHeadline">
	<h1>{lang}admin.packageServer.package.list{/lang}</h1>
</header>

<div class="contentNavigation">
	{hascontent}
	<nav>
		{content}
			{if $__wcf->session->getPermission('admin.packageServer.canAddPackage')}
				<ul>
					<li>
						<a href="{link controller='PackageAdd'}{/link}" title="" class="button">
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
			<h2>{lang}admin.packageServer.package.list{/lang}</h2>
		</header>

		<table class="table">
			<thead>
				<tr>
					<th class="columnTitle">{lang}wcf.acp.packageserver.package.package{/lang}</th>
					<th class="columnText">{lang}wcf.acp.packageserver.package.version{/lang}</th>
					
					{event name='headColumns'}
				</tr>
			</thead>

			<tbody>
				{content}
					{foreach from=$items item=versions key=package}
						{foreach from=$versions item=version}
							<tr>
								<td class="columnIcon">
									<a href="{link controller='DeletePackageVersion' package=$package version=$version}{/link}" title="Löschen" class="jsTooltip"><span class="icon icon16 icon-remove"></span></a>
								</td>
								<td class="columnTitle"><p>{$package}</p></td>
								<td class="columnText"><p>{$version}</p></td>

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
			{if $__wcf->session->getPermission('admin.packageServer.canAddPackage')}
				<ul>
					<li>
						<a href="{link controller='PackageAdd'}{/link}" title="" class="button">
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

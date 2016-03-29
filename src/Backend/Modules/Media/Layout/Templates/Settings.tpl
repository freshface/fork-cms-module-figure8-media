{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblMedia}</h2>
</div>

{form:settings}

	<div class="box">
		<div class="horizontal">
			<div class="heading">
				<h3>{$lblPagination|ucfirst}</h3>
			</div>
			<div class="options">
				<label for="featerhApiKey">{$lblFeatherApiKey|ucfirst}</label>
				{$txtFeatherApiKey} {$txtFeatherApiKeyError}
				<span class="helpTxt">
					<a href="https://creativesdk.adobe.com/myapps.html" target="_blank">https://creativesdk.adobe.com</a>
				</span>
			</div>
		</div>
	</div>

	<div class="box">
		<div class="horizontal">
			<div class="heading">
				<h3>{$lblResolutions|ucfirst}</h3>
			</div>
			<div class="options">
				{option:dgResolutions}
				<div class="dataGridHolder">
					<div class="tableHeading">
						<h3>{$lblResolutions|ucfirst}</h3>
					</div>
					{$dgResolutions}
				</div>
			{/option:dgResolutions}
			</div>
		</div>
	</div>

	

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}

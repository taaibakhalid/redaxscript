rs.modules.TextareaResizer.validate = config =>
{
	const CONFIG = {...rs.modules.TextareaResizer.config, ...config};
	const textarea = document.querySelectorAll(CONFIG.selector);

	if (textarea)
	{
		window.autosize(textarea);
	}
};

/* run as needed */

if (rs.modules.TextareaResizer.frontend.init)
{
	rs.modules.TextareaResizer.validate(rs.modules.TextareaResizer.frontend.config);
}
if (rs.modules.TextareaResizer.backend.init)
{
	rs.modules.TextareaResizer.validate(rs.modules.TextareaResizer.backend.config);
}

rs.modules.TableSorter.execute = config =>
{
	const CONFIG =
	{
		...rs.modules.TableSorter.config,
		...config
	};
	const tbodyList = document.querySelectorAll(CONFIG.selector);

	if (tbodyList)
	{
		tbodyList.forEach(tbody =>
		{
			const dragula = window.dragula(
			[
				tbody
			], CONFIG.dragula);

			/* prevent scroll */

			tbody.addEventListener('touchmove', event => event.preventDefault());

			/* handle dragend */

			dragula.on('dragend', () =>
			{
				const childrenList = tbody.childNodes;

				window.fetch(CONFIG.sortUrl,
				{
					method: 'POST',
					headers:
					{
						'Content-Type': 'application/json',
						'X-Requested-With': 'XMLHttpRequest'
					},
					body: JSON.stringify(
					{
						table: rs.registry.tableParameter,
						rankArray: Object.keys(childrenList).map((childrenValue => childrenList[childrenValue].id.replace('row-', '')))
					})
				})
				.then(() => CONFIG.reload ? location.reload() : null);
			});
		});
	}
};

/* run as needed */

if (rs.modules.TableSorter.init && rs.modules.TableSorter.dependency)
{
	rs.modules.TableSorter.execute(rs.modules.TableSorter.config);
}

((window, document) =>
{
	'use strict'

	XF.SylphianLinkWarning = XF.Element.newHandler({
		options: {
			url: '',
			trusted: '',
		},

		trustedDomains: [],

		init ()
		{
			if (!this.options.url)
			{
				return
			}

			this.trustedDomains = String(this.options.trusted).split('\n').filter(Boolean)

			const onClick = this.onClick.bind(this)
			XF.on(document, 'click', onClick)
			XF.on(document, 'auxclick', onClick)
		},

		isTrusted (host)
		{
			return this.trustedDomains.some(domain => host === domain || host.endsWith('.' + domain))
		},

		onClick (e)
		{
			if (e.defaultPrevented || (e.type === 'auxclick' && e.button !== 1))
			{
				return
			}

			const link = e.target.closest('a.link--external[href]')
			if (!link)
			{
				return
			}

			let destination
			try
			{
				destination = new URL(link.href)
			}
			catch (error)
			{
				return
			}

			if (
				!['http:', 'https:'].includes(destination.protocol)
				|| destination.host === window.location.host
				|| this.isTrusted(destination.hostname)
			)
			{
				return
			}

			e.preventDefault()

			const separator = this.options.url.includes('?') ? '&' : '?'
			XF.loadOverlay(this.options.url + separator + 'url=' + encodeURIComponent(destination.href))
		},
	})

	XF.Element.register('sylphian-link-warning', 'XF.SylphianLinkWarning')
})(window, document)

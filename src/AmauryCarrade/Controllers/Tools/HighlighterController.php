<?php
namespace AmauryCarrade\Controllers\Tools;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class HighlighterController
{
	public function highlight(Application $app, Request $request, $format = 'html')
	{
		$r = $request->request;

		$raw_quote            = $r->get('quote');

		$remove_dates         = $r->has('remove_dates');
		$remove_bots          = $r->get('remove_bots');
		$colors               = $r->get('colors');
		$color_date           = $r->get('color_date');
		$italic_actions       = $r->has('italic_actions');
		$lines_separator      = $r->get('lines_separator');
		$nick_prefixes        = $r->get('nick_prefixes');
		$nick_prefixes_color  = $r->get('nick_prefixes_color');
		$output_format        = $r->get('output_format');

		$formatted_quote = null;

		if (!empty($raw_quote))
		{
			$command = array('python3 ' . $app['root_folder'] . '/lib/ChatLogHighlighter/highlighter.py');

			if ($remove_dates)
				$command[] = '--remove-dates';

			if (!empty($remove_bots))
				$command[] = '--remove-bots ' . escapeshellarg($remove_bots);

			if (!empty($colors))
				$command[] = '--colors ' . escapeshellarg($colors);

			if (!empty($color_date))
				$command[] = '--color-date ' . escapeshellarg($color_date);

			if ($italic_actions)
				$command[] = '--italic-actions';

			if (!empty($lines_separator))
				$command[] = '--lines-separator ' . escapeshellarg($lines_separator);

			if (!empty($nick_prefixes))
				$command[] = '--nick-prefixes ' . escapeshellarg($nick_prefixes);

			if (!empty($nick_prefixes_color))
				$command[] = '--nick-prefixes-color ' . escapeshellarg($nick_prefixes_color);

			if (!empty($output_format))
				$command[] = '--output-format ' . escapeshellarg($output_format);

			$command[] = escapeshellarg($raw_quote);

			$output = array();
			exec(implode(' ', $command), $output);

			$formatted_quote = implode("\n", $output);
		}

		$data = array(
			'success'              => !empty($formatted_quote),

			'quote'                => $raw_quote,
			'formatted_quote'      => $formatted_quote,

			'remove_dates'         => $remove_dates, //
			'remove_bots'          => $remove_bots,  //
			'colors'               => $colors,       //
			'color_date'           => $color_date,   //
			'italic_actions'       => $italic_actions,  //
			'lines_separator'      => $lines_separator, //
			'nick_prefixes'        => $nick_prefixes, //
			'nick_prefixes_color'  => $nick_prefixes_color, //
			'output_format'        => $output_format
		);

		switch ($format) {
			case 'json':
				return $app->json($data);
				break;
			
			default:
				return $app['twig']->render('tools/misc/chat_highlighter.html.twig', $data);
		}
	}
}
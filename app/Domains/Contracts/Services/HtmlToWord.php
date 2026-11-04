<?php

namespace App\Domains\Contracts\Services;

use DOMDocument;
use DOMElement;
use DOMNode;
use PhpOffice\PhpWord\Element\TextRun;

/**
 * Converte o HTML do TipTap para o .docx preservando negrito,
 * itálico, sublinhado e tachado (em vez de strip_tags).
 */
class HtmlToWord
{
    private const SIZE_PT = 10; // mesmo tamanho (sz=20) do template

    /** Cada parágrafo = lista de segmentos ['text','b','i','u','s'] */
    public function paragraphs(string $html): array
    {
        $doc = new DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><body>' . $html . '</body>', LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $body = $doc->getElementsByTagName('body')->item(0);
        $paragraphs = [];
        $current = [];
        $this->walk($body, [], $paragraphs, $current);
        $this->flush($paragraphs, $current);

        return $paragraphs;
    }

    /** Parágrafos <w:p> completos: substituem o parágrafo do ${clausulas}. */
    public function toParagraphsXml(string $html): string
    {
        $xml = '';
        foreach ($this->paragraphs($html) as $segments) {
            $xml .= '<w:p><w:pPr><w:spacing w:after="40" w:before="100" w:line="252" w:lineRule="auto"/>'
                . '<w:jc w:val="both"/></w:pPr>' . $this->runsXml($segments) . '</w:p>';
        }

        return $xml !== '' ? $xml : '<w:p/>';
    }

    /** Runs para campos que ficam no meio de um parágrafo (outros dados, condições). */
    public function toTextRun(string $html): TextRun
    {
        $run = new TextRun();
        foreach ($this->paragraphs($html) as $i => $segments) {
            if ($i > 0) {
                $run->addTextBreak();
            }
            foreach ($segments as $s) {
                $run->addText($s['text'], [
                    'size'          => self::SIZE_PT,
                    'bold'          => $s['b'],
                    'italic'        => $s['i'],
                    'underline'     => $s['u'] ? 'single' : null,
                    'strikethrough' => $s['s'],
                ]);
            }
        }

        return $run;
    }

    private function runsXml(array $segments): string
    {
        $xml = '';
        foreach ($segments as $s) {
            $rPr = ($s['b'] ? '<w:b/><w:bCs/>' : '')
                . ($s['i'] ? '<w:i/><w:iCs/>' : '')
                . ($s['s'] ? '<w:strike/>' : '')
                . ($s['u'] ? '<w:u w:val="single"/>' : '')
                . '<w:sz w:val="' . (self::SIZE_PT * 2) . '"/><w:szCs w:val="' . (self::SIZE_PT * 2) . '"/>';
            $text = htmlspecialchars($s['text'], ENT_XML1 | ENT_QUOTES, 'UTF-8');
            $xml .= '<w:r><w:rPr>' . $rPr . '</w:rPr><w:t xml:space="preserve">' . $text . '</w:t></w:r>';
        }

        return $xml;
    }

    private function walk(DOMNode $node, array $fmt, array &$paragraphs, array &$current): void
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                if ($child->nodeValue !== '') {
                    $current[] = ['text' => $child->nodeValue] + $fmt;
                }
                continue;
            }
            if (!$child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);
            $childFmt = $fmt;

            switch ($tag) {
                case 'strong': case 'b':  $childFmt['b'] = true; break;
                case 'em': case 'i':      $childFmt['i'] = true; break;
                case 'u':                 $childFmt['u'] = true; break;
                case 's': case 'strike': case 'del': $childFmt['s'] = true; break;
                case 'h1': case 'h2': case 'h3': case 'h4': case 'h5': case 'h6':
                    $childFmt['b'] = true; break;
            }

            if ($tag === 'br') {
                $this->flush($paragraphs, $current);
            } elseif (in_array($tag, ['ul', 'ol'], true)) {
                $this->flush($paragraphs, $current);
                $n = 0;
                foreach ($child->childNodes as $li) {
                    if ($li instanceof DOMElement && strtolower($li->tagName) === 'li') {
                        $current[] = ['text' => $tag === 'ol' ? (++$n) . '. ' : '• '] + $fmt;
                        $this->walk($li, $fmt, $paragraphs, $current);
                        $this->flush($paragraphs, $current);
                    }
                }
            } elseif (in_array($tag, ['p', 'div', 'blockquote', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) {
                $this->flush($paragraphs, $current);
                $before = count($paragraphs);
                $this->walk($child, $childFmt, $paragraphs, $current);
                $vazio = $current === [] && count($paragraphs) === $before;
                $this->flush($paragraphs, $current, $vazio && $tag === 'p'); // <p></p> = linha em branco
            } else {
                $this->walk($child, $childFmt, $paragraphs, $current);
            }
        }
    }

    private function flush(array &$paragraphs, array &$current, bool $force = false): void
    {
        if ($current === [] && !$force) {
            return;
        }
        $paragraphs[] = array_map(
            fn ($s) => $s + ['b' => false, 'i' => false, 'u' => false, 's' => false],
            $current
        );
        $current = [];
    }
}
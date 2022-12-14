<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\security\spam;

use serve\config\Config;
use serve\security\spam\gibberish\Gibberish;
use serve\utility\Str;

use function array_unique;
use function array_values;
use function count;
use function in_array;
use function is_array;
use function preg_match_all;
use function preg_replace;
use function preg_split;
use function sort;
use function strlen;
use function strtolower;
use function trim;

/**
 * SPAM manager.
 *
 * @author Joe J. Howard
 */
class SpamProtector
{
    /**
     * Gibberish detector.
     *
     * @var \serve\security\spam\gibberish\Gibberish
     */
    private $gibberish;

    /**
     * Config loader.
     *
     * @var \serve\config\Config
     */
    private $config;

    /**
     * Constructor.
     *
     * @param \serve\security\spam\gibberish\Gibberish $gibberish Gibberish detector
     * @param \serve\config\Config                     $config    Config loader
     */
    public function __construct(Gibberish $gibberish, Config $config)
    {
        $this->gibberish = $gibberish;

        $this->config = $config;
    }

    /**
     * Checks if text is SPAM.
     *
     * @param  string $text Text to check
     * @return bool
     */
    public function isSpam(string $text): bool
    {
        if ($this->listContains($this->config->get('spam.blacklist.constructs'), $text))
        {
            return true;
        }
        elseif ($this->listContains($this->config->get('spam.blacklist.urls'), $text))
        {
            return true;
        }
        elseif ($this->listContains($this->config->get('spam.blacklist.words'), $text))
        {
            return true;
        }
        elseif ($this->listContains($this->config->get('spam.blacklist.html'), $text))
        {
            return true;
        }
        elseif ($this->gibberish->test($text))
        {
            return true;
        }

        return false;
    }

    /**
     * Gets a SPAM rating.
     *
     * @param  string $text Text to check
     * @return int
     */
    public function rating(string $text): int
    {
        $rating = 0;

        // Get statistics
        $linkCount      = $this->countLinks($text);
        $bodyCount      = strlen(trim($text));
        $keyWords       = $this->countGraylisted($text);

        // Rate links
        if ($linkCount > 2)
        {
            $rating = ($rating - $linkCount);
        }

        // Rate Length
        if ($bodyCount > 20)
        {
            $rating++;
        }
        else
        {
            $rating--;
        }

        // Keyword matches
        if ($keyWords > 0)
        {
            $rating += ($rating - ($keyWords * 2));
        }

        return $rating;
    }

    /**
     * Checks if an IP address is whitelisted.
     *
     * @param  string $ipAddresses The IP address to check
     * @return bool
     */
    public function isIpWhiteListed(string $ipAddresses): bool
    {
        return $this->listContains($this->config->get('spam.whitelist.ipaddresses'), $ipAddresses);
    }

    /**
     * Checks if an IP address is blacklisted.
     *
     * @param  string $ipAddresses The IP address to check
     * @return bool
     */
    public function isIpBlacklisted(string $ipAddresses): bool
    {
        return $this->listContains($this->config->get('spam.blacklist.ipaddresses'), $ipAddresses);
    }

    /**
     * Blacklists an ip address.
     *
     * @param string $ipAddresses The IP to blacklist
     */
    public function blacklistIpAddress(string $ipAddresses): void
    {
        $this->config->set('spam.blacklist.ipaddresses', $this->addToList($ipAddresses, $this->config->get('spam.blacklist.ipaddresses')));

        $this->config->save();
    }

    /**
     * Remove an ip address from the blacklist.
     *
     * @param string $ipAddresses The IP to remove
     */
    public function unBlacklistIpAddress(string $ipAddresses): void
    {
        $this->config->set('spam.blacklist.ipaddresses', $this->removeFromList($ipAddresses, $this->config->get('spam.blacklist.ipaddresses')));

        $this->config->save();
    }

    /**
     * whitelists an ip address.
     *
     * @param string $ipAddresses The IP to whitelist
     */
    public function whitelistIpAddress(string $ipAddresses): void
    {
        $this->config->set('spam.whitelist.ipaddresses', $this->addToList($ipAddresses, $this->config->get('spam.whitelist.ipaddresses')));

        $this->config->save();
    }

    /**
     * Remove an ip address from the whitelist.
     *
     * @param string $ipAddresses The IP to remove
     */
    public function unWhitelistIpAddress(string $ipAddresses): void
    {
        $this->config->set('spam.whitelist.ipaddresses', $this->removeFromList($ipAddresses, $this->config->get('spam.whitelist.ipaddresses')));

        $this->config->save();
    }

    /**
     * Check if a list contains a word.
     *
     * @param  array  $list The array to check in
     * @param  string $term The term to check for
     * @return bool
     */
    private function listContains(array $list, string $term): bool
    {
        $term = strtolower($term);

        foreach ($list as $item)
        {
            $item = strtolower($item);

            if ($item === $term || Str::contains($term, $item))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Add an item to a list.
     *
     * @param  string $item The item to add to the list
     * @param  array  $list The array to alter
     * @return array
     */
    private function addToList(string $item, array $list): array
    {
        $list[] = $item;

        $list = array_unique(array_values($list));

        sort($list);

        return $list;
    }

    /**
     * Remove an item from a list.
     *
     * @param  string $item The item to add to the list
     * @param  array  $list The array to alter
     * @return array
     */
    private function removeFromList(string $item, array $list): array
    {
        foreach ($list as $i => $value)
        {
            if ($value === $item)
            {
                unset($list[$i]);

                break;
            }
        }

        $list = array_unique(array_values($list));

        sort($list);

        return $list;
    }

    /**
     * Counts how many links are in text.
     *
     * @param  string $text Text to check
     * @return int
     */
    private function countLinks(string $text): int
    {
        $count = 0;

        preg_match_all('/<a[^>]+href=([\'"])(.+?)\1[^>]*>/', $text, $htmlLinks);

        preg_match_all('/http.+/', $text, $rawLinks);

        if (is_array($rawLinks) && isset($rawLinks[0]))
        {
            $count += count($rawLinks[0]);
        }

        if (is_array($htmlLinks) && isset($htmlLinks[0]))
        {
            $count += count($htmlLinks[0]);
        }

        return $count;
    }

    /**
     * Count how many graylisted words are in a string of text.
     *
     * @param  string $text Text to check
     * @return int
     */
    private function countGraylisted(string $text): int
    {
        $count = 0;

        $words = preg_split("/[^\w]*([\s]+[^\w]*|$)/", $text, -1, PREG_SPLIT_NO_EMPTY);

        $constructs = $this->config->get('spam.graylist.constructs');

        $urls = $this->config->get('spam.graylist.urls');

        $terms = $this->config->get('spam.graylist.words');

        $html = $this->config->get('spam.graylist.html');

        foreach ($words as $word)
        {
            $word = trim(preg_replace("/([\.\,\;\:\"\'!])/", '', $word));

            if (!empty($word) && !in_array($word, ['!', '.', ',', '-', '&', ';', ':', '"', "'"]))
            {
                if ($this->listContains($constructs, $word) || $this->listContains($urls, $word) || $this->listContains($terms, $word) || $this->listContains($html, $word))
                {
                    $count++;
                }
            }
        }

        return $count;
    }
}

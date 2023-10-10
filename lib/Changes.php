<?php

class Changes
{
    /** @var string[] */
    protected $tags;

    /** @var int[] */
    protected $tagMap;

    /**
     * @return void
     */
    protected function loadTags()
    {
        $tags = array_reduce(explode("\n", `git tag -l`), function (array $carry, string $tag) {
            $tag = trim($tag);

            if ($tag !== '' && preg_match('/^\d+\.\d+\.\d+$/', $tag)) {
                $carry[] = $tag;
            }

            return $carry;
        }, []);

        usort($tags, function ($a, $b) {
            list($aMajor, $aMinor, $aPatch) = explode('.', $a, 3);
            list($bMajor, $bMinor, $bPatch) = explode('.', $b, 3);

            if ($aMajor != $bMajor) {
                return $aMajor > $bMajor ? 1 : -1;
            }

            if ($aMinor != $bMinor) {
                return $aMinor > $bMinor ? 1 : -1;
            }

            return $aPatch > $bPatch ? 1 : -1;
        });

        $this->tags = $tags;
        $this->tagMap = array_flip($tags);
    }

    /**
     * @return string[]
     */
    protected function &getTags(): array
    {
        if ($this->tags === null) {
            $this->loadTags();
        }

        return $this->tags;
    }

    /**
     * @return int[]
     */
    protected function &getTagMap(): array
    {
        if ($this->tagMap === null) {
            $tags = &$this->getTags();

            $this->tagMap = array_flip($tags);
        }

        return $this->tagMap;
    }

    /**
     * @param string $tag
     * @param int $offset
     * @return string|null
     */
    public function getTag(string $tag, int $offset = 0): ?string
    {
        $map = &$this->getTagMap();

        $i = $map[$tag] ?? null;

        if ($i === null) {
            return null;
        }

        $i += $offset;

        return $this->getTags()[$i] ?? null;
    }

    /**
     * @return string|null
     */
    public function getLastTag(): ?string
    {
        $tags = &$this->getTags();

        $tag = end($tags);

        return $tag === false ? null : $tag;
    }

    /**
     * @param string $tag
     * @param string|null $fromTag
     * @return array|null
     */
    public function getLog(string $tag, string $fromTag = null): ?array
    {
        $tag = $this->getTag($tag);
        $fromTag = $fromTag === null ? $this->getTag($tag, -1) : $fromTag;

        if ($tag === null || $fromTag === null) {
            return null;
        }

        return array_reduce(explode("\n", `git log --pretty=format:"%s" $fromTag..$tag`), function (array $carry, string $message) {
            $message = trim($message);

            if ($message !== '') {
                $carry[] = $message;
            }

            return $carry;
        }, []);
    }
}

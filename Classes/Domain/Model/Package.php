<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license http://www.opensource.org/licenses/mit-license.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * @package TimonKreis\TkComposer\Domain\Model
 */
class Package extends AbstractEntity
{
    public const ACCESS_PRIVATE = 1;
    public const ACCESS_PROTECTED = 2;
    public const ACCESS_PUBLIC = 3;

    public const TYPE_GIT = 1;

    public const RELATION_UNDEFINED = 0;
    public const RELATION_DIRECT = 1;
    public const RELATION_CLONED = 2;

    /** @see https://getcomposer.org/doc/04-schema.md#name */
    public const NAME_PATTERN = '[a-z0-9]([_.-]?[a-z0-9]+)*\/[a-z0-9](([_.]?|-{0,2})[a-z0-9]+)*';

    /**
     * @var string[]
     */
    protected $readableTypes = [
        self::TYPE_GIT => 'git',
    ];

    /**
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     * @var string
     */
    protected $repositoryUrl = '';

    /**
     * @var int
     */
    protected $type = 0;

    /**
     * @var int
     */
    protected $access = 0;

    /**
     * @var int
     */
    protected $relation = 0;

    /**
     * @var string
     */
    protected $hash = '';

    /**
     * @var string
     */
    protected $latestTag = '';

    /**
     * @var string
     */
    protected $packageName = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $tagsStatus = '';

    /**
     * Returns the repositoryUrl
     *
     * @return string
     */
    public function getRepositoryUrl() : string
    {
        return $this->repositoryUrl;
    }

    /**
     * Sets the repositoryUrl
     *
     * @param string $repositoryUrl
     * @return void
     */
    public function setRepositoryUrl(string $repositoryUrl) : void
    {
        $this->repositoryUrl = $repositoryUrl;
    }

    /**
     * Get the type
     *
     * @return int
     */
    public function getType() : int
    {
        return $this->type;
    }

    /**
     * Get readable type
     *
     * @return string
     */
    public function getReadableType() : string
    {
        return $this->readableTypes[$this->type] ?? '';
    }

    /**
     * Set the type
     *
     * @param int $type
     * @return void
     */
    public function setType(int $type) : void
    {
        $this->type = $type;
    }

    /**
     * Get the access
     *
     * @return int
     */
    public function getAccess() : int
    {
        return $this->access;
    }

    /**
     * Set the access
     *
     * @param int $access
     * @return void
     */
    public function setAccess(int $access) : void
    {
        $this->access = $access;
    }

    /**
     * Returns the hash
     *
     * @return string
     */
    public function getHash() : string
    {
        return $this->hash;
    }

    /**
     * Sets the hash
     *
     * @param string $hash
     * @return void
     */
    public function setHash(string $hash) : void
    {
        $this->hash = $hash;
    }

    /**
     * Returns the relation
     *
     * @return int
     */
    public function getRelation() : int
    {
        return $this->relation;
    }

    /**
     * Sets the relation
     *
     * @param int $relation
     * @return void
     */
    public function setRelation(int $relation) : void
    {
        $this->relation = $relation;
    }

    /**
     * Returns the latestTag
     *
     * @return string
     */
    public function getLatestTag() : string
    {
        return $this->latestTag;
    }

    /**
     * Sets the latestTag
     *
     * @param string $latestTag
     * @return void
     */
    public function setLatestTag(string $latestTag) : void
    {
        $this->latestTag = $latestTag;
    }

    /**
     * Returns the tags status
     *
     * @return array
     */
    public function getTagsStatus() : array
    {
        return $this->tagsStatus ? json_decode($this->tagsStatus, true) : [];
    }

    /**
     * Sets the tags status
     *
     * @param array $tagsStatus
     * @return void
     */
    public function setTagsStatus(array $tagsStatus) : void
    {
        $this->tagsStatus = json_encode($tagsStatus, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Returns the packageName
     *
     * @return string
     */
    public function getPackageName() : string
    {
        return $this->packageName;
    }

    /**
     * Sets the packageName
     *
     * @param string $packageName
     * @return void
     */
    public function setPackageName(string $packageName) : void
    {
        $this->packageName = $packageName;
    }

    /**
     * Returns the description
     *
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription(string $description) : void
    {
        $this->description = $description;
    }
}

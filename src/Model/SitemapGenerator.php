<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Model;

use Magento\Framework\UrlInterface;
use Siteimprove\Magento\Model\Sitemap as SitemapModel;
use Symfony\Component\Filesystem\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;

class SitemapGenerator
{
    /**
     * @var \Siteimprove\Magento\Api\TokenInterface
     */
    protected $_token;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_directoryList;

    /**
     * @var \Siteimprove\Magento\Model\SitemapFactory
     */
    protected $_sitemapFactory;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $_storeRepository;

    /**
     * @var \Magento\Framework\Url\ScopeResolverInterface
     */
    protected $_urlScopeResolver;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_rootDirectory;

    public function __construct(
        \Siteimprove\Magento\Api\TokenInterface $token,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Siteimprove\Magento\Model\SitemapFactory $sitemapFactory,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Framework\Url\ScopeResolverInterface $urlScopeResolver,
        \Magento\Config\Model\Config\Reader\Source\Deployed\DocumentRoot $documentRoot
    ) {
        $this->_token = $token;
        $this->_filesystem = $filesystem;
        $this->_directoryList = $directoryList;
        $this->_sitemapFactory = $sitemapFactory;
        $this->_storeRepository = $storeRepository;
        $this->_urlScopeResolver = $urlScopeResolver;
        $this->_rootDirectory = $filesystem->getDirectoryWrite($documentRoot->getPath());
    }

    public function getPathSecret()
    {
        return hash('sha256', $this->_token->getToken());
    }

    /**
     * @param string $sitemapFilename
     * @param int $storeId
     * @return SitemapModel
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getSitemapModel(int $storeId): SitemapModel
    {
        list($modelPath, $pathInMedia, $filename) = $this->getModelPaths($storeId);
        return $this->_sitemapFactory->create([
            'data' => [
                'sitemap_filename' => $filename,
                'sitemap_path'     => $modelPath,
                'store_id'         => $storeId,
            ],
        ]);
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function isSitemapGenerated(int $storeId): bool
    {
        list($modelPath, $pathInMedia, $filename) = $this->getModelPaths($storeId, false);
        $reader= $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
        return $reader->isExist("{$pathInMedia}/{$filename}");
    }

    /**
     * @param string $sitemapFilename
     * @param int $storeId
     * @return string
     * @throws FileSystemException
     */
    public function getSitemapUrl(int $storeId): string
    {
        list($modelPath, $pathInMedia, $filename) = $this->getModelPaths($storeId);
        /** @var \Magento\Framework\Url\ScopeInterface $urlResolver */
        $urlResolver = $this->_urlScopeResolver->getScope($storeId);
        $baseMediaUrl = $urlResolver->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return $baseMediaUrl . rtrim($pathInMedia, '/') . '/' . $filename;
    }

    /**
     * @param int $storeId
     * @param bool $ensureDir
     * @return string[]
     * @throws FileSystemException
     */
    private function getModelPaths(int $storeId, bool $ensureDir = true)
    {
        $pathSecret = $this->getPathSecret();
        $pathInMedia = "siteimprove-maps/{$pathSecret}";
        $filename = "store_{$storeId}.xml";
        $mediaWriter = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $rootPath = $this->_rootDirectory->getAbsolutePath();
        $sitemapMapDirectory = $mediaWriter->getAbsolutePath($pathInMedia);
        $driver = $mediaWriter->getDriver();
        $modelPath = $driver->getRelativePath($rootPath, $sitemapMapDirectory);
        if ((new Filesystem)->isAbsolutePath($modelPath)) {
            throw new FileSystemException(__(
                'Siteimprove XML path ("%1") is not under Magento document root ("%2")', $rootPath, $sitemapMapDirectory
            ));
        }

        if ($ensureDir && !$mediaWriter->isDirectory($pathInMedia))
            $mediaWriter->create($pathInMedia); {
        }

        return ["/{$modelPath}", $pathInMedia, $filename];
    }
}

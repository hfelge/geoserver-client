<?php

declare(strict_types=1);

use Hfelge\GeoServerClient\Tests\TestCaseWithGeoServerClient;
use Hfelge\GeoServerClient\GeoServerException;
use PHPUnit\Framework\Attributes\Test;

class StyleManagerTest extends TestCaseWithGeoServerClient
{
    protected string $workspace = 'phpunit_ws';
    protected string $datastore = 'phpunit_ds';
    protected string $featureType = 'phpunit_layer';
    protected string $styleName = 'phpunit_style';

    protected string $sld = <<<SLD
<?xml version="1.0" encoding="UTF-8"?>
<StyledLayerDescriptor version="1.0.0"
  xmlns="http://www.opengis.net/sld"
  xmlns:ogc="http://www.opengis.net/ogc"
  xmlns:xlink="http://www.w3.org/1999/xlink"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://www.opengis.net/sld StyledLayerDescriptor.xsd">
  <NamedLayer>
    <Name>default</Name>
    <UserStyle>
      <Title>PHPUnit Style</Title>
      <FeatureTypeStyle>
        <Rule>
          <PolygonSymbolizer>
            <Fill>
              <CssParameter name="fill">#FF0000</CssParameter>
            </Fill>
          </PolygonSymbolizer>
        </Rule>
      </FeatureTypeStyle>
    </UserStyle>
  </NamedLayer>
</StyledLayerDescriptor>
SLD;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client->workspaceManager->createWorkspace($this->workspace);

        $this->client->datastoreManager->createPostGISDatastore($this->workspace, $this->datastore, [
            'host'     => getenv('GEOSERVER_DB_HOST')     ?: 'db',
            'port'     => getenv('GEOSERVER_DB_PORT')     ?: '5432',
            'database' => getenv('GEOSERVER_DB_NAME')     ?: 'db',
            'user'     => getenv('GEOSERVER_DB_USER')     ?: 'db',
            'passwd'   => getenv('GEOSERVER_DB_PASSWORD') ?: 'db',
        ]);

        $this->client->featureTypeManager->createFeatureType($this->workspace, $this->datastore, [
            'name'       => $this->featureType,
            'nativeName' => $this->featureType,
            'title'      => 'PHPUnit FeatureType',
            'srs'        => 'EPSG:4326',
        ]);

        $this->client->layerManager->publishLayer($this->featureType);
    }

    protected function tearDown(): void
    {
        $this->client->styleManager->deleteStyle($this->styleName);
        $this->client->layerManager->deleteLayer($this->featureType);
        $this->client->featureTypeManager->deleteFeatureType($this->workspace, $this->datastore, $this->featureType);
        $this->client->datastoreManager->deleteDatastore($this->workspace, $this->datastore);
        $this->client->workspaceManager->deleteWorkspace($this->workspace, true);
    }

    #[Test]
    public function it_can_list_styles(): void
    {
        $styles = $this->client->styleManager->getStyles();
        $this->assertIsArray($styles);
        $this->assertArrayHasKey('styles', $styles);
    }

    #[Test]
    public function it_can_create_workspace_style(): void
    {
        $this->markTestIncomplete('StyleManager::createWorkspaceStyle() does not work as expected.');

        $result = $this->client->styleManager->createWorkspaceStyle(
            $this->workspace,
            $this->styleName,
            $this->sld
        );
        $this->assertTrue($result);
        $this->assertTrue($this->client->styleManager->styleExistsInWorkspace($this->workspace,$this->styleName));
    }

    #[Test]
    public function it_returns_false_when_style_exists(): void
    {
        $this->client->styleManager->createWorkspaceStyle($this->workspace, $this->styleName, $this->sld);
        $result = $this->client->styleManager->createWorkspaceStyle($this->workspace, $this->styleName, $this->sld);
        $this->assertFalse($result);
    }

    #[Test]
    public function it_can_update_style(): void
    {
        $this->markTestIncomplete('StyleManager::updateStyle() does not work as expected.');

        $this->client->styleManager->createWorkspaceStyle($this->workspace, $this->styleName, $this->sld);
        $updatedSld = str_replace('#FF0000', '#0000FF', $this->sld);
        $result = $this->client->styleManager->updateStyle($this->styleName, $updatedSld);
        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_when_updating_nonexistent_style(): void
    {
        $result = $this->client->styleManager->updateStyle('not_there', $this->sld);
        $this->assertFalse($result);
    }

    #[Test]
    public function it_can_get_existing_style(): void
    {
        $this->markTestIncomplete('StyleManager::getStyle() does not work as expected.');

        $this->client->styleManager->createWorkspaceStyle($this->workspace, $this->styleName, $this->sld);
        $style = $this->client->styleManager->getStyle($this->styleName);
        $this->assertEquals($this->styleName, $style['style']['name']);
    }

    #[Test]
    public function it_returns_false_when_getting_nonexistent_style(): void
    {
        $style = $this->client->styleManager->getStyle('not_there');
        $this->assertFalse($style);
    }

    #[Test]
    public function it_can_delete_existing_style(): void
    {
        $this->markTestIncomplete('StyleManager::deleteStyle() does not work as expected.');

        $this->client->styleManager->createWorkspaceStyle($this->workspace, $this->styleName, $this->sld);
        $result = $this->client->styleManager->deleteStyle($this->styleName);
        $this->assertTrue($result);
        $this->assertFalse($this->client->styleManager->styleExists($this->styleName));
    }

    #[Test]
    public function it_returns_false_when_deleting_nonexistent_style(): void
    {
        $result = $this->client->styleManager->deleteStyle('not-there-style');
        $this->assertFalse($result);
    }

    // Optional – falls assignStyleToLayer beibehalten wird:
    #[Test]
    public function it_returns_false_when_assigning_style_fails(): void
    {
        $result = $this->client->styleManager->assignStyleToLayer('not_existing_layer', 'not_existing_style');
        $this->assertFalse($result);
    }
}

<?php

namespace Hfelge\GeoServerClient;

class GeoServerClient
{
    public WorkspaceManager   $workspaceManager;
    public DatastoreManager   $datastoreManager;
    public FeatureTypeManager $featureTypeManager;
    public LayerManager       $layerManager;
    public StyleManager       $styleManager;
    public UserManager       $userManager;
    public RoleManager       $roleManager;

    protected ?bool $cachedAvailability = null;

    public function __construct(
        protected string $baseUrl,
        protected string $username,
        protected string $password,
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');

        $this->workspaceManager   = new WorkspaceManager($this);
        $this->datastoreManager   = new DatastoreManager($this);
        $this->featureTypeManager = new FeatureTypeManager($this);
        $this->layerManager       = new LayerManager($this);
        $this->styleManager       = new StyleManager($this);
        $this->userManager        = new UserManager($this);
        $this->roleManager        = new RoleManager($this);
    }

    public function isAvailable(bool $forceCheck = false): bool
    {
        if (!$forceCheck && $this->cachedAvailability !== null) {
            return $this->cachedAvailability;
        }

        try {
            $this->request('GET', '/rest/about/version.json');
            $this->cachedAvailability = true;
        } catch (GeoServerException) {
            $this->cachedAvailability = false;
        }

        return $this->cachedAvailability;
    }

    public function publishFeatureLayer(
        string $workspace,
        string $datastore,
        array $featureTypeDefinition,
        array $connectionParameters = []
    ): bool {
        if (!$this->workspaceManager->workspaceExists($workspace)) {
            $this->workspaceManager->createWorkspace($workspace);
        }

        if (!$this->datastoreManager->datastoreExists($workspace, $datastore)) {
            $defaultParams = [
                'host'     => 'localhost',
                'port'     => '5432',
                'database' => 'gis',
                'user'     => 'geo_user',
                'passwd'   => 'secret',
            ];

            $this->datastoreManager->createPostGISDatastore(
                $workspace,
                $datastore,
                array_merge($defaultParams, $connectionParameters)
            );
        }

        $name = $featureTypeDefinition['name'] ?? null;
        if (!$name) {
            throw new \InvalidArgumentException("FeatureType 'name' is required.");
        }

        if (!$this->featureTypeManager->featureTypeExists($workspace, $datastore, $name)) {
            $this->featureTypeManager->createFeatureType($workspace, $datastore, $featureTypeDefinition);
        }

        return $this->layerManager->publishLayer($name);
    }


    public function request(string $method, string $url, ?string $body = null, array $headers = []): array
    {
        $ch = curl_init($this->baseUrl . $url);

        $defaultHeaders = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        // Aber NUR wenn nicht schon manuell gesetzt:
        $allHeaders = $this->mergeHeaders($defaultHeaders, $headers);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_USERPWD        => "{$this->username}:{$this->password}",
            CURLOPT_HTTPHEADER     => $allHeaders,
        ]);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $responseBody = curl_exec($ch);
        $statusCode   = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $error        = curl_error($ch);

        curl_close($ch);

        if ($error !== '') {
            throw new GeoServerException(0, $url, "CURL error: $error");
        }

        if ($statusCode >= 400) {
            throw new GeoServerException($statusCode, $url, $responseBody ?? '');
        }

        return [
            'status' => $statusCode,
            'body'   => $responseBody,
        ];
    }

    private function mergeHeaders(array $defaultHeaders, array $customHeaders): array
    {
        $map = [];

        // Zuerst: Standard-Header eintragen
        foreach ($defaultHeaders as $header) {
            [$name, $value] = explode(':', $header, 2);
            $map[strtolower(trim($name))] = trim($value);
        }

        // Dann: Custom-Header eintragen (überschreibt ggf.)
        foreach ($customHeaders as $header) {
            [$name, $value] = explode(':', $header, 2);
            $map[strtolower(trim($name))] = trim($value);
        }

        // Rückgabe im richtigen Format
        return array_map(
            fn($name, $value) => $name . ': ' . $value,
            array_keys($map),
            $map
        );
    }
}

<?php 
namespace App\Services;
use App\Http\Requests\PWARequest;
use Exception;
use Illuminate\Support\Facades\Log;
use Dipokhalder\EnvEditor\EnvEditor;
use App\Models\PWA;


class PWAService {



    public EnvEditor $envService;
    public function __construct(EnvEditor $envEditor)
    {
        $this->envService = $envEditor;
    }

    /**
     * @throws Exception
     */
    public function list()
    {
         try {
              return PWA::first();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(PWARequest $request)
    {
        $newMedia = [];
        try {
            $pwa =  PWA::first();
            if(!$pwa) {
                $pwa = PWA::create(['id' => 1]);
            }

            $oldSplash = $pwa->getMedia('pwa_splash');
            $oldIcons = $pwa->getMedia('pwa_icon');
            $splash = null;
            $icons = null;

            // Keep the current files until every conversion and environment update succeeds.
            if ($request->hasFile('pwa_splash')) {
                $splash = $pwa->addMediaFromRequest('pwa_splash')->toMediaCollection('pwa_splash');
                $newMedia[] = $splash;
            }
            if ($request->hasFile('pwa_icon')) {
                $icons = $pwa->addMediaFromRequest('pwa_icon')->toMediaCollection('pwa_icon');
                $newMedia[] = $icons;
            }

            $envData = [];
            foreach ($icons ? ['D_72x72', 'D_96x96', 'D_128x128', 'D_144x144', 'D_152x152', 'D_192x192', 'D_384x384', 'D_512x512'] : [] as $conversion) {
                $envData[$conversion] = $icons->getUrl($conversion);
            }
            foreach ($splash ? ['D_640x1136', 'D_750x1334', 'D_828x1792', 'D_1125x2436', 'D_1242x2208', 'D_1242x2688', 'D_1536x2048', 'D_1668x2224', 'D_1668x2388', 'D_2048x2732'] : [] as $conversion) {
                $envData[$conversion] = $splash->getUrl($conversion);
            }

            if ($envData !== []) {
                // EnvEditor reads, backs up, and rewrites the whole file per call.
                $this->envService->addData($envData);
            }

            if ($splash) {
                $oldSplash->each(fn ($media) => $media->delete());
            }
            if ($icons) {
                $oldIcons->each(fn ($media) => $media->delete());
            }

            return $pwa->fresh();
        } catch (Exception $exception) {
            foreach ($newMedia as $media) {
                $media->delete();
            }
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }
}

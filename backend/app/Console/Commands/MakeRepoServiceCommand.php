<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRepoServiceCommand extends Command
{
    protected $signature = "make:repo+service {--model=}";
    protected $description = "Tạo Repository & Service từ tên Model";

    public function handle()
    {
        $modelName = $this->option("model");

        if (!$modelName) {
            $this->error("❌ Vui lòng nhập --model=ModelName");
            return;
        }

        $this->generateRepository($modelName);
        $this->generateService($modelName);

        $this->info("✅ Repository và Service cho {$modelName} đã được tạo.");
    }

    protected function generateRepository($model)
    {
        $interfaceName = "{$model}RepositoryInterface";
        $eloquentName = "{$model}RepositoryEloquent";

        $interfacePath = app_path("Repositories/Interfaces/{$interfaceName}.php");
        $eloquentPath = app_path("Repositories/Eloquent/{$eloquentName}.php");

        // Interface
        if (!File::exists($interfacePath)) {
            File::ensureDirectoryExists(dirname($interfacePath));
            File::put(
                $interfacePath,
                "<?php

namespace App\Repositories\Interfaces;

interface {$interfaceName}
{
    // Định nghĩa các hàm
}
"
            );
        }

        // Eloquent Repository
        if (!File::exists($eloquentPath)) {
            File::ensureDirectoryExists(dirname($eloquentPath));
            File::put(
                $eloquentPath,
                "<?php

namespace App\Repositories\Eloquent;

use App\Models\\{$model};
use App\Repositories\Interfaces\\{$interfaceName};

class {$eloquentName} implements {$interfaceName}
{
    protected \${$this->camel($model)};

    public function __construct({$model} \${$this->camel($model)})
    {
        \$this->{$this->camel($model)} = \${$this->camel($model)};
    }

    // Triển khai các hàm
}
"
            );
        }
    }

    protected function generateService($model)
    {
        $interfaceName = "{$model}ServiceInterface";
        $implName = "{$model}Service";

        $interfacePath = app_path("Services/Interfaces/{$interfaceName}.php");
        $servicePath = app_path("Services/Impl/{$implName}.php");

        // Interface
        if (!File::exists($interfacePath)) {
            File::ensureDirectoryExists(dirname($interfacePath));
            File::put(
                $interfacePath,
                "<?php

namespace App\Services\Interfaces;

interface {$interfaceName}
{
    // Định nghĩa các hàm
}
"
            );
        }

        // Impl
        if (!File::exists($servicePath)) {
            File::ensureDirectoryExists(dirname($servicePath));
            File::put(
                $servicePath,
                "<?php

namespace App\Services\Impl;

use App\Services\Interfaces\\{$interfaceName};
use App\Repositories\Interfaces\\{$model}RepositoryInterface;

class {$implName} implements {$interfaceName}
{
    protected \${$this->camel($model)}Repository;

    public function __construct({$model}RepositoryInterface \${$this->camel($model)}Repository)
    {
        \$this->{$this->camel($model)}Repository = \${$this->camel($model)}Repository;
    }

    // Triển khai các hàm
}
"
            );
        }
    }

    protected function camel($string)
    {
        return Str::camel($string);
    }
}

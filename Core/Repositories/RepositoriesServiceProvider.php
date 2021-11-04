<?php


namespace Modules\Core\Repositories;


use App\Providers\AppServiceProvider;
use Modules\Core\Repositories\Auth\ModuleRepository;
use Modules\Core\Repositories\Auth\SubModuleRepository;
use Modules\Core\Repositories\Auth\UserRepository;
use Modules\Core\Repositories\Contracts\Auth\ModuleInterface;
use Modules\Core\Repositories\Contracts\Auth\SubModuleInterface;
use Modules\Core\Repositories\Contracts\Auth\UserInterface;
use Modules\Core\Repositories\Contracts\BrandRepositoryInterface;
use Modules\Core\Repositories\Contracts\CategoryRepositoryInterface;
use Modules\Core\Repositories\Contracts\Fmcg\CompanyRepositoryInterface;
use Modules\Core\Repositories\Contracts\Fmcg\FmcgBrandRepositoryInterface;
use Modules\Core\Repositories\Contracts\Fmcg\FmcgProductRepositoryInterface;
use Modules\Core\Repositories\Contracts\InvestmentRequirementRepositoryInterface;
use Modules\Core\Repositories\Contracts\Location\DistrictRepositoryInterface;
use Modules\Core\Repositories\Contracts\Location\DivisionRepositoryInterface;
use Modules\Core\Repositories\Contracts\Location\UpazilaRepositoryInterface;
use Modules\Core\Repositories\Contracts\OrderRepositoryInterface;
use Modules\Core\Repositories\Contracts\ProductRepositoryInterface;
use Modules\Core\Repositories\Contracts\RetailProductRepositoryInterface;
use Modules\Core\Repositories\Contracts\RetailUserRepositoryInterface;
use Modules\Core\Repositories\Contracts\SkillCertificationReqRepositoryInterface;
use Modules\Core\Repositories\Contracts\SubCategoryRepositoryInterface;
use Modules\Core\Repositories\Contracts\UnitRepositoryInterface;
use Modules\Core\Repositories\Fmcg\CompanyRepository;
use Modules\Core\Repositories\Fmcg\FmcgProductRepository;
use Modules\Core\Repositories\Location\DistrictRepository;
use Modules\Core\Repositories\Location\DivisionRepository;
use Modules\Core\Repositories\Location\UpazilaRepository;
use Modules\Core\Repositories\Order\OrderRepository;
use Modules\Core\Repositories\Product\BrandRepository;
use Modules\Core\Repositories\Product\CategoryRepository;
use Modules\Core\Repositories\Product\CategoryTypeRepository;
use Modules\Core\Repositories\Contracts\CategoryTypeRepositoryInterface;
use Modules\Core\Repositories\Product\FmcgBrandRepository;
use Modules\Core\Repositories\Product\InvestmentRequirementRepository;
use Modules\Core\Repositories\Product\ProductRepository;
use Modules\Core\Repositories\Product\RetailProductRepository;
use Modules\Core\Repositories\Product\SkillCertificationReqRepository;
use Modules\Core\Repositories\Product\SubCategoryRepository;
use Modules\Core\Repositories\Product\UnitRepository;
use Modules\Core\Repositories\User\RetailUserRepository;
use Modules\SignUp\Repositories\ComponentCategoryRepository;
use Modules\SignUp\Repositories\ConTracts\ComponentCategoryRepositoryInterface;
use Modules\SignUp\Repositories\ConTracts\GenderEducationAssetRoleComponentRepositoryInterface;
use Modules\SignUp\Repositories\ConTracts\GenderEducationAssetRoleRepositoryInterface;
use Modules\SignUp\Repositories\Contracts\SignUpOfferDetailsRepositoryInterface;
use Modules\SignUp\Repositories\ConTracts\SignUpOfferRepositoryInterface;
use Modules\SignUp\Repositories\GenderEducationAssetRoleComponentRepository;
use Modules\SignUp\Repositories\GenderEducationAssetRoleRepository;
use Modules\SignUp\Repositories\SignUpOfferDetailsRepository;
use Modules\SignUp\Repositories\SignUpOfferRepository;

class RepositoriesServiceProvider extends AppServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);
        $this->app->bind(RetailProductRepositoryInterface::class, RetailProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(RetailUserRepositoryInterface::class, RetailUserRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(DivisionRepositoryInterface::class, DivisionRepository::class);
        $this->app->bind(DistrictRepositoryInterface::class, DistrictRepository::class);
        $this->app->bind(UpazilaRepositoryInterface::class, UpazilaRepository::class);


        /*
         * Auth Interface Bind
         */
        $this->app->bind(ModuleInterface::class, ModuleRepository::class);
        $this->app->bind(SubModuleInterface::class, SubModuleRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(CategoryTypeRepositoryInterface::class, CategoryTypeRepository::class);
        $this->app->bind(SubCategoryRepositoryInterface::class, SubCategoryRepository::class);
        $this->app->bind(SkillCertificationReqRepositoryInterface::class, SkillCertificationReqRepository::class);
        $this->app->bind(UnitRepositoryInterface::class, UnitRepository::class);
        $this->app->bind(InvestmentRequirementRepositoryInterface::class, InvestmentRequirementRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(FmcgBrandRepositoryInterface::class, FmcgBrandRepository::class);
        $this->app->bind(FmcgProductRepositoryInterface::class, FmcgProductRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(GenderEducationAssetRoleRepositoryInterface::class, GenderEducationAssetRoleRepository::class);
        $this->app->bind(GenderEducationAssetRoleComponentRepositoryInterface::class, GenderEducationAssetRoleComponentRepository::class);
        $this->app->bind(SignUpOfferRepositoryInterface::class, SignUpOfferRepository::class);
        $this->app->bind(SignUpOfferDetailsRepositoryInterface::class, SignUpOfferDetailsRepository::class);
        $this->app->bind(ComponentCategoryRepositoryInterface::class, ComponentCategoryRepository::class);

    }

}

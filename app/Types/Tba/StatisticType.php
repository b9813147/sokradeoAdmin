<?php

namespace App\Types\Tba;

use Exception;
use App\Helpers\Enum\Constant;

abstract class StatisticType
{
    use Constant;

    // 科技運用
    const PickupResult          = 1;
    const PickupRange           = 2;
    const PickupNameLst         = 3;
    const PickupGrp             = 4;
    const PickupEachGrp         = 5;
    const PickupNthGrp          = 6;
    const PickupRight           = 7;
    const PickupWrong           = 8;
    const PickupOption          = 9;
    const PickupDiff            = 10;
    const PickupNoDiff          = 11;
    const PickupRtoW            = 12;
    const PickupWtoR            = 13;
    const TimerResult           = 14;
    const ScoBrdLoad            = 15;
    const QuesLoad              = 16;
    const PopQuesLoad           = 17;
    const PrmpQues              = 18;
    const ReAtmpAnsStrt         = 19;
    const ShowAnsLoad           = 20;
    const StatChartLoad         = 21;
    const BarChart              = 22;
    const PieChart              = 23;
    const GrpBarChart           = 24;
    const GrpPieChart           = 25;
    const GrpVerStackChart      = 26;
    const GrpCorrBarChart       = 27;
    const BuzrLoad              = 28;
    const FdbkCollLoad          = 29;
    const PgPushLoad            = 30;
    const DiffPush              = 31;
    const PushIndMember         = 32;
    const PushGrpMember         = 33;
    const PushInGrpMember       = 34;
    const PushAllMember         = 35;
    const PushFastPgPush        = 36;
    const Beampgload            = 37;
    const Hitalivestrt          = 38;
    const Hitasendvdo           = 39;
    const Playvdo               = 40;
    const WrkCmp                = 41;
    const Beampagecmp           = 42;
    const WrkSpaceCmp           = 43;
    const Prsnlpgcmp            = 44;
    const Hitaclientcmp         = 45;
    const Takepic               = 46;

    // 科技指數
    const TechDex               = 47;

    const PedaDex               = 48;
    const MaGrp                 = 49;
    const MaWhole               = 50;
    const MaLect                = 51;
    const MaPsnl                = 52;
    const MaSCD                 = 53;
    const MaMulAss              = 54;

    const CntDex                = 55; // 教材實踐
    const CntDesign             = 56; // 教學設計
    const CntProcess            = 57; // 教學過程
    const CntEffect             = 58; // 教學效果
    const CntTech               = 59; // 技術應用
    const CntInno               = 60; // 融合創新

    const MaTest                = 61; // 全班測驗

    const TotlScoBrdLoad        = 62;
    const LdrBrdNum             = 63;
    const LdrBrdPoint           = 64;
    const LdrBrdGrp             = 65;


    //
    public static function check($type)
    {
        throw new Exception('please implement');
    }

    //
    public static function list()
    {
        throw new Exception('please implement');
    }

    //
    public static function getTechFunResults()
    {
        return [1, 14, 15, 16, 17, 18, 19, 20, 21, 28, 29, 30, 37, 38, 39, 40, 41, 46];
    }

    //
    public static function getTechInteractIdx()
    {
        return 47;
    }

    //
    public static function getMethodAnal()
    {
        return [
                'value' => StatisticType::getMethodAnalValue(),
                'items' => StatisticType::getMethodAnalItems(),
        ];
    }

    //
    public static function getMethodAnalValue()
    {
        return 48;
    }

    //
    public static function getMethodAnalItems()
    {
        return [49, 54, 52, 61, 53, 50];
    }

    //
    public static function getContentIdx()
    {
        return [
                'value' => StatisticType::getContentIdxValue(),
                'items' => StatisticType::getContentIdxItems(),
        ];
    }

    //
    public static function getContentIdxValue()
    {
        return 55;
    }

    //
    public static function getContentIdxItems()
    {
        return [56, 57, 58, 59, 60];
    }

}

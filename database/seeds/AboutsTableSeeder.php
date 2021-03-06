<?php

use Illuminate\Database\Seeder;

use App\About;

class AboutsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('abouts')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        About::create([
        	'content' => '
				昆山稳卓汽车配件有限公司是德国威格曼汽车集团( WEGMANN automotive GmbH & Co. KG）在华投资的全资子公司。公司在中国总部和制造工厂位于江苏省昆山市。
				|
				威格曼汽车集团(www.wegmann-automotive.com）总部位于德国的伍尔茨堡，是全球领先的汽车车轮平衡块、汽车电池电极和特种平衡块产品的专业制造商。集团旗下包括Hofmann品牌、Perfect品牌，Venjong品牌。客户涵盖全球各大汽车主机厂和各个区域售后市场。汽车车轮平衡块年销售超过10亿只。
				|
				昆山稳卓汽车配件有限公司（昆山稳卓）注册成立于2008年，自2009年起开始投产，产品主要出口欧洲和美洲等海外市场。从2011年起，昆山稳卓在昆山市的巴城工业园区开始投资兴建制造基地，1期和2期工程的10000平米厂房已经竣工并投入使用。
				|
				昆山稳卓严格按照威格曼汽车集团全球的制造标准进行设计和生产，制造工艺全部实现自动化和半自动化，确保了产品质量的稳定性、一致性和可追溯性。系中国目前唯一一家采用批量自动化制造工艺生产车轮平衡块的企业。产品的质量标准严格遵循更为严格的企业标准和TS16949质量体系的要求。
				|
				昆山稳卓已经成为奥迪、宝马、奔驰、福特、长城汽车等汽车制造厂的车辆平衡块供应商，产品同时服务于国内快速增长的售后市场及欧洲、美洲、澳洲等海外市场。
				|
				昆山稳卓秉承德国企业严谨、认真、创新的传统，并结合本地文化特点，积极倡导“ 纪律、尊重、高效、工作与生活和谐平衡”的企业文化理念。
				|
				伴随着在华业务的不断高速增长，我们竭诚欢迎有识之士加入昆山稳卓的团队，与稳卓团队一起见证你的成长和发展'
        ]);
    }
}

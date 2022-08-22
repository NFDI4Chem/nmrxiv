<?php

namespace Database\Seeders;

use App\Models\License;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class LicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $licenses = array(
            /*Creative Commons*/
            array(
                'title'              => 'CC0 1.0 Universal Public Domain Dedication',
                'slug'               => 'cc0-1.0',
                'spdx_id'            => 'CC0-1.0',
                'url'                => 'https://creativecommons.org/publicdomain/zero/1.0/legalcode',
                'description'        => 'The person who associated a work with this deed has dedicated the work to the public domain by waiving all of his or her rights to the work worldwide under copyright law, including all related and neighboring rights, to the extent allowed by law.You can copy, modify, distribute and perform the work, even for commercial purposes, all without asking permission. See Other Information below.',
                'body'               => '<b>Creative Commons License Deed</b><br><br><b>No Copyright</b> <br>The person who associated a work with this deed has dedicated the work to the public domain by waiving all of his or her rights to the work worldwide under copyright law, including all related and neighboring rights, to the extent allowed by law.<br>You can copy, modify, distribute and perform the work, even for commercial purposes, all without asking permission. See Other Information below.<br><br><b>Other Information</b><li>In no way are the patent or trademark rights of any person affected by CC0, nor are the rights that other persons may have in the work or in how the work is used, such as publicity or privacy rights.</li><li>Unless expressly stated otherwise, the person who associated a work with this deed makes no warranties about the work, and disclaims liability for all uses of the work, to the fullest extent permitted by applicable law.</li><li>When using or citing the work, you should not imply endorsement by the author or the affirmer.</li><br><br><b>Other Rights</b><br>The use of a work free of known copyright restrictions may be otherwise regulated or limited. The work or its use may be subject to personal data protection laws, publicity, image, or privacy rights that allow a person to control how their voice, image or likeness is used, or other restrictions or limitations under applicable law.<br><a href="https://creativecommons.org/faq/#When_are_publicity_rights_relevant.3F" style="color:#019DBB;" target="_blank">Learn more.</a><br><br><b>Endorsement</b><br>In some jurisdictions, wrongfully implying that an author, publisher or anyone else endorses your use of a work may be unlawful.<br><a href="https://creativecommons.org/faq/#Do_I_need_to_be_aware_of_anything_else_when_providing_attribution_or_credit.3F" style="color:#019DBB;" target="_blank">Learn more.</a><br><br><b>Citation</b><br>Copy and paste the HTML provided into your webpage to easily cite this work.<br><a href="https://wiki.creativecommons.org/wiki/CC0_FAQ#Do_I_have_to_attribute_the_person_who_applied_CC0_to_their_work.3F" style="color:#019DBB;" target="_blank">Learn more.</a><br><br><b>Who is the affirmer?</b><br>The affirmer is the person who surrendered rights to the work worldwide using CC0, to the extent allowable by law. It may be the original author of the work or another person who may have had some copyright or related or neighboring legal rights in the work.<h3 style="color:#F5346C;">Note: </h3><b>This is a human-readable summary of (and not a substitute for) the </b><a href="https://creativecommons.org/publicdomain/zero/1.0/legalcode" style="color:#019DBB;" target="_blank">license</a>.',
                'category'           => 'Creative Commons'
            ),
            array(
                'title'              => 'Attribution-NonCommercial-ShareAlike 4.0 International',
                'slug'               => 'cc-by-nc-sa-4.0',
                'spdx_id'            => 'CC-BY-NC-SA-4.0',
                'url'                => 'https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode',
                'description'        => 'You are free to: <br><b>Share</b> — copy and redistribute the material in any medium or format.<br><b>Adapt</b> — remix, transform, and build upon the material.',
                'body'               => '<b>Creative Commons License Deed</b><br>This is a human-readable summary of (and not a substitute for) the <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode" style="color:#019DBB;" target="_blank">license</a>.<br><b>You are free to:</b><li>Adapt — remix, transform, and build upon the material</li><li>The licensor cannot revoke these freedoms as long as you follow the license terms.</li><br>Under the following terms:<li><b>Attribution</b> — You must give appropriate credit, provide a link to the license, and indicate if changes were made. You may do so in any reasonable manner, but not in any way that suggests the licensor endorses you or your use.</li><li><b>NonCommercial</b> — You may not use the material for commercial purposes.</li><li><b>ShareAlike</b> — If you remix, transform, or build upon the material, you must distribute your contributions under the same license as the original.</li><li><b>No additional restrictions</b> — You may not apply legal terms or technological measures that legally restrict others from doing anything the license permits.</li><br><b>Notices:</b><li>You do not have to comply with the license for elements of the material in the public domain or where your use is permitted by an applicable exception or limitation.</li><li>No warranties are given. The license may not give you all of the permissions necessary for your intended use. For example, other rights such as publicity, privacy, or moral rights may limit how you use the material.</li>',
                'category'           => 'Creative Commons'
                ),
            array(
                'title'              => 'Attribution-ShareAlike 4.0 International',
                'slug'               => 'cc-by-sa-4.0',
                'spdx_id'            => 'CC-BY-SA-4.0',
                'url'                => 'https://creativecommons.org/licenses/by-sa/4.0/legalcode',
                'description'        => 'You are free to: <br><b>Share</b> — copy and redistribute the material in any medium or format.<br><b>Adapt</b> — remix, transform, and build upon the material for any purpose, even commercially.',
                'body'               => '<b>Creative Commons License Deed</b><br><b>You are free to:</b><li>Adapt — remix, transform, and build upon the material for any purpose, even commercially.</li><li>This license is acceptable for Free Cultural Works.</li><li>The licensor cannot revoke these freedoms as long as you follow the license terms.</li><br>Under the following terms:<li><b>Attribution</b> — You must give appropriate credit, provide a link to the license, and indicate if changes were made. You may do so in any reasonable manner, but not in any way that suggests the licensor endorses you or your use.</li><li><b>ShareAlike</b> — If you remix, transform, or build upon the material, you must distribute your contributions under the same license as the original.</li><li><b>No additional restrictions</b> — You may not apply legal terms or technological measures that legally restrict others from doing anything the license permits.</li><br><b>Notices:</b><li>You do not have to comply with the license for elements of the material in the public domain or where your use is permitted by an applicable exception or limitation.</li><li>No warranties are given. The license may not give you all of the permissions necessary for your intended use. For example, other rights such as publicity, privacy, or moral rights may limit how you use the material.</li><h3 style="color:#F5346C;">Note: </h3><b>This is a human-readable summary of (and not a substitute for) the </b><a href="https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode" style="color:#019DBB;" target="_blank">license</a>.',
                'category'           => 'Creative Commons'
                ),
            array(
                'title'              => 'Attribution-ShareAlike 3.0 Unported',
                'slug'               => 'cc-by-sa-3.0',
                'spdx_id'            => 'CC-BY-SA-3.0',
                'url'                => 'https://creativecommons.org/licenses/by-sa/3.0/legalcode',
                'description'        => 'You are free to: <br><b>Share</b> — copy and redistribute the material in any medium or format.<br><b>Adapt</b> — remix, transform, and build upon the material for any purpose, even commercially',
                'body'               => '<b>Creative Commons License Deed</b><br><b>You are free to:</b><li>Adapt — remix, transform, and build upon the material for any purpose, even commercially.</li><li>This license is acceptable for Free Cultural Works.</li><li>The licensor cannot revoke these freedoms as long as you follow the license terms.</li><br>Under the following terms:<li><b>Attribution</b> — You must give appropriate credit, provide a link to the license, and indicate if changes were made. You may do so in any reasonable manner, but not in any way that suggests the licensor endorses you or your use.</li><li><b>ShareAlike</b> — If you remix, transform, or build upon the material, you must distribute your contributions under the same license as the original.</li><li><b>No additional restrictions</b> — You may not apply legal terms or technological measures that legally restrict others from doing anything the license permits.</li><br><b>Notices:</b><li>You do not have to comply with the license for elements of the material in the public domain or where your use is permitted by an applicable exception or limitation.</li><li>No warranties are given. The license may not give you all of the permissions necessary for your intended use. For example, other rights such as publicity, privacy, or moral rights may limit how you use the material.</li> <h3 style="color:#F5346C;">Note: </h3><b>This is a human-readable summary of (and not a substitute for) the </b><a href="https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode" style="color:#019DBB;" target="_blank">license</a>.',
                'category'           => 'Creative Commons'
                ),
            array(
                'title'              => 'Attribution 4.0 International',
                'slug'               => 'cc-by-4.0',
                'spdx_id'            => 'CC-BY-4.0',
                'url'                => 'https://creativecommons.org/licenses/by/4.0/legalcode',
                'description'        => 'You are free to: <br><b>Share</b> — copy and redistribute the material in any medium or format.<br><b>Adapt</b> — remix, transform, and build upon the material for any purpose, even commercially.',
                'body'               => '<b>Creative Commons License Deed</b><br><b>You are free to:</b><li>Adapt — remix, transform, and build upon the material for any purpose, even commercially.</li><li>The licensor cannot revoke these freedoms as long as you follow the license terms.</li><br>Under the following terms:<li><b>Attribution</b> — You must give appropriate credit, provide a link to the license, and indicate if changes were made. You may do so in any reasonable manner, but not in any way that suggests the licensor endorses you or your use.</li><li><b>No additional restrictions</b> — You may not apply legal terms or technological measures that legally restrict others from doing anything the license permits.</li><br><b>Notices:</b><li>You do not have to comply with the license for elements of the material in the public domain or where your use is permitted by an applicable exception or limitation.</li><li>No warranties are given. The license may not give you all of the permissions necessary for your intended use. For example, other rights such as publicity, privacy, or moral rights may limit how you use the material.</li><h3 style="color:#F5346C;">Note: </h3><b>This is a human-readable summary of (and not a substitute for) the </b><a href="https://creativecommons.org/licenses/by/4.0/legalcode" style="color:#019DBB;" target="_blank">license</a>.',
                'category'           => 'Creative Commons'
            ),
            array(
                'title'              => 'Attribution-NonCommercial 4.0 International',
                'slug'               => 'cc-by-nc-4.0',
                'spdx_id'            => 'CC-BY-NC-4.0',
                'url'                => 'https://creativecommons.org/licenses/by-nc/4.0/legalcode',
                'description'        => 'You are free to: <br><b>Share</b> — copy and redistribute the material in any medium or format.<br><b>Adapt</b> — remix, transform, and build upon the material.',
                'body'               => '<b>Creative Commons License Deed</b><br>This is a human-readable summary of (and not a substitute for) the <a href="https://creativecommons.org/licenses/by-nc/4.0/legalcode" style="color:#019DBB;" target="_blank">license</a>.<br><b>You are free to:</b><li>Adapt — remix, transform, and build upon the material.</li><br>Under the following terms:<li><b>Attribution</b> — You must give appropriate credit, provide a link to the license, and indicate if changes were made. You may do so in any reasonable manner, but not in any way that suggests the licensor endorses you or your use.</li><li><b>NonCommercial</b> — You may not use the material for commercial purposes</li><li><b>No additional restrictions</b> — You may not apply legal terms or technological measures that legally restrict others from doing anything the license permits.</li><br><b>Notices:</b><li>You do not have to comply with the license for elements of the material in the public domain or where your use is permitted by an applicable exception or limitation.</li><li>No warranties are given. The license may not give you all of the permissions necessary for your intended use. For example, other rights such as publicity, privacy, or moral rights may limit how you use the material.</li><h3 style="color:#F5346C;">Note: </h3><b>This is a human-readable summary of (and not a substitute for) the </b><a href="https://creativecommons.org/licenses/by-nc/4.0/legalcode" style="color:#019DBB;" target="_blank">license</a>.',
                'category'           => 'Creative Commons'
                ),
            array(
                'title'              => 'Attribution 3.0 Unported',
                'slug'               => 'cc-by-3.0',
                'spdx_id'            => 'CC-BY-3.0',
                'url'                => 'https://creativecommons.org/licenses/by/3.0/legalcode',
                'description'        => 'You are free to: <br><b>Share</b> — copy and redistribute the material in any medium or format.<br><b>Adapt</b> — remix, transform, and build upon the material for any purpose, even commercially.',
                'body'               => '<b>Creative Commons License Deed</b><br><b>You are free to:</b><li>Adapt — remix, transform, and build upon the material for any purpose, even commercially.</li><li>The licensor cannot revoke these freedoms as long as you follow the license terms.</li><br>Under the following terms:<li><b>Attribution</b> — You must give appropriate credit, provide a link to the license, and indicate if changes were made. You may do so in any reasonable manner, but not in any way that suggests the licensor endorses you or your use.</li><li><b>No additional restrictions</b> — You may not apply legal terms or technological measures that legally restrict others from doing anything the license permits.</li><br><b>Notices:</b><li>You do not have to comply with the license for elements of the material in the public domain or where your use is permitted by an applicable exception or limitation.</li><li>No warranties are given. The license may not give you all of the permissions necessary for your intended use. For example, other rights such as publicity, privacy, or moral rights may limit how you use the material.</li><h3 style="color:#F5346C;">Note: </h3><b>This is a human-readable summary of (and not a substitute for) the </b><a href="https://creativecommons.org/licenses/by/3.0/legalcode" style="color:#019DBB;" target="_blank">license</a>.',
                'category'           => 'Creative Commons'
                ),
            array(
                'title'              => 'Attribution 3.0 IGO',
                'slug'               => 'cc-by-3.0-igo',
                'spdx_id'            => 'CC-BY-3.0-IGO',
                'url'                => 'https://creativecommons.org/licenses/by/3.0/igo/legalcode',
                'description'        => 'You are free to: <br><b>Share</b> — copy and redistribute the material in any medium or format.<br><b>Adapt</b> — remix, transform, and build upon the material for any purpose, even commercially.',
                'body'               => '<b>Creative Commons License Deed</b><br><b>You are free to:</b><li>Adapt — remix, transform, and build upon the material for any purpose, even commercially.</li><li>The licensor cannot revoke these freedoms as long as you follow the license terms.</li><br>Under the following terms:<li><b>Attribution</b> — You must give appropriate credit, provide a link to the license, and indicate if changes were made. You may do so in any reasonable manner, but not in any way that suggests the licensor endorses you or your use.</li><li><b>No additional restrictions</b> — You may not apply legal terms or technological measures that legally restrict others from doing anything the license permits.</li><br><b>Notices:</b><li>When the Licensor is an intergovernmental organization, disputes will be resolved by mediation and arbitration unless otherwise agreed.</li><li>You do not have to comply with the license for elements of the material in the public domain or where your use is permitted by an applicable exception or limitation.</li><li>No warranties are given. The license may not give you all of the permissions necessary for your intended use. For example, other rights such as publicity, privacy, or moral rights may limit how you use the material.</li><h3 style="color:#F5346C;">Note: </h3><b>This is a human-readable summary of (and not a substitute for) the </b><a href="https://creativecommons.org/licenses/by/3.0/igo/legalcode" style="color:#019DBB;" target="_blank">license</a>.',
                'category'           => 'Creative Commons'
                ),
            array(
                'title'              => 'Attribution-NonCommercial-ShareAlike 3.0 IGO',
                'slug'               => 'cc-by-3.0-igo',
                'spdx_id'            => 'CC-BY-NC-SA-3.0-IGO',
                'url'                => 'https://creativecommons.org/licenses/by-nc-sa/3.0/igo/legalcode',
                'description'        => 'You are free to: <br><b>Share</b> — copy and redistribute the material in any medium or format.<br><b>Adapt</b> — remix, transform, and build upon the material.',
                'body'               => '<b>Creative Commons License Deed</b><b>You are free to:</b><li>Adapt — remix, transform, and build upon the material.</li><br>Under the following terms:<li><b>Attribution</b> — You must give appropriate credit, provide a link to the license, and indicate if changes were made. You may do so in any reasonable manner, but not in any way that suggests the licensor endorses you or your use.</li><li><b>NonCommercial</b> — You may not use the material for commercial purposes</li><li><b>ShareAlike</b> — If you remix, transform, or build upon the material, you must distribute your contributions under the same license as the original.</li><li><b>No additional restrictions</b> — You may not apply legal terms or technological measures that legally restrict others from doing anything the license permits.</li><br><b>Notices:</b><li>When the Licensor is an intergovernmental organization, disputes will be resolved by mediation and arbitration unless otherwise agreed.</li><li>You do not have to comply with the license for elements of the material in the public domain or where your use is permitted by an applicable exception or limitation.</li><li>No warranties are given. The license may not give you all of the permissions necessary for your intended use. For example, other rights such as publicity, privacy, or moral rights may limit how you use the material.</li><h3 style="color:#F5346C;">Note:</h3><b>This is a human-readable summary of (and not a substitute for) the</b> <a href="https://creativecommons.org/licenses/by-nc-sa/3.0/igo/legalcode" style="color:#019DBB;" target="_blank">license</a>.',
                'category'           => 'Creative Commons'
                ),
            array(
                'title'              => 'Attribution-NoDerivatives 4.0 International',
                'slug'               => 'cc-by-nd-4.0',
                'spdx_id'            => 'CC-BY-ND-4.0',
                'url'                => 'https://creativecommons.org/licenses/by-nd/4.0/legalcode',
                'description'        => 'You are free to: <br><b>Share</b> — copy and redistribute the material in any medium or format for any purpose, even commercially.',
                'body'               => '<b>Creative Commons License Deed</b><br><b>You are free to:</b><li>for any purpose, even commercially.</li><li>The licensor cannot revoke these freedoms as long as you follow the license terms.</li><br>Under the following terms:<li><b>Attribution</b> — You must give appropriate credit, provide a link to the license, and indicate if changes were made. You may do so in any reasonable manner, but not in any way that suggests the licensor endorses you or your use.</li><li><b>NoDerivatives</b> — If you remix, transform, or build upon the material, you may not distribute the modified material.</li><li><b>No additional restrictions</b> — You may not apply legal terms or technological measures that legally restrict others from doing anything the license permits.</li><br><b>Notices:</b><li>You do not have to comply with the license for elements of the material in the public domain or where your use is permitted by an applicable exception or limitation.</li><li>No warranties are given. The license may not give you all of the permissions necessary for your intended use. For example, other rights such as publicity, privacy, or moral rights may limit how you use the material.</li><h3 style="color:#F5346C;">Note: </h3><b>This is a human-readable summary of (and not a substitute for) the </b><a href="https://creativecommons.org/licenses/by-nd/4.0/legalcode" style="color:#019DBB;" target="_blank">license</a>.',
                'category'           => 'Creative Commons'
                ),
            array(
                'title'              => 'Attribution-NonCommercial-NoDerivatives 4.0 International',
                'slug'               => 'cc-by-nc-nd-4.0',
                'spdx_id'            => 'CC-BY-NC-ND-4.0',
                'url'                => 'https://creativecommons.org/licenses/by-nc-nd/4.0/legalcode',
                'description'        => 'You are free to: <br><b>Share</b> — copy and redistribute the material in any medium or format.',
                'body'               => '<b>Creative Commons License Deed</b><br><b>You are free to:</b><li>Share — copy and redistribute the material in any medium or format.</li><li>The licensor cannot revoke these freedoms as long as you follow the license terms.</li><br>Under the following terms:<li><b>Attribution</b> — You must give appropriate credit, provide a link to the license, and indicate if changes were made. You may do so in any reasonable manner, but not in any way that suggests the licensor endorses you or your use.</li><li><b>NonCommercial</b> - You may not use the material for commercial purposes.</li><li><b>NoDerivatives</b> — If you remix, transform, or build upon the material, you may not distribute the modified material.</li><li><b>No additional restrictions</b> — You may not apply legal terms or technological measures that legally restrict others from doing anything the license permits.</li><br><b>Notices:</b><li>You do not have to comply with the license for elements of the material in the public domain or where your use is permitted by an applicable exception or limitation.</li><li>No warranties are given. The license may not give you all of the permissions necessary for your intended use. For example, other rights such as publicity, privacy, or moral rights may limit how you use the material.</li><h3 style="color:#F5346C;">Note: </h3><b>This is a human-readable summary of (and not a substitute for) the </b><a href="https://creativecommons.org/licenses/by-nc-nd/4.0/legalcode" style="color:#019DBB;" target="_blank">license</a>.',
                'category'           => 'Creative Commons'
                ),
            /*GPL*/

            /*Open Data Commons*/


            /*Community Data License*/


            /*Special*/

            /*Other*/

            );
        foreach ($licenses as $license) {
            $license = License::create([
                'title'        => $license['title'],
                'slug'         => $license['slug'],
                'spdx_id'      => $license['spdx_id'],
                'url'          => $license['url'],
                'description'  => $license['description'],
                'body'         => $license['body'],
                'category'     => $license['category'],
            ]);
        }
    }
}

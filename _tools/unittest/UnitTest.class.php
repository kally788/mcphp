<?php
/**
 * Created by IntelliJ IDEA.
 * User: zhujili
 * Date: 14-6-13
 * Time: 下午4:17
 */

class UnitTest {
    /*
    //断言为真
    protected function AssertTrue(){

    }
    //断言为假
    protected function AssertFalse(){

    }
    //判断输出是否和预期的相等
    protected function AssertEquals(){

    }
    //断言结果是否(大于)某个值
    protected function AssertGreaterThan(){

    }
    //断言结果是否(小于)某个值
    protected function AssertLessThan(){

    }
    //断言结果是否(大于等于)某个值
    protected function AssertGreaterThanOrEqual(){

    }
    //断言结果是否(小于等于)某个值
    protected function AssertLessThanOrEqual(){

    }
    //判断输入是否包含指定的值
    protected function AssertContains(){

    }
    //判断是否属于指定类型
    protected function AssertType(){

    }
    //判断是否为空值
    protected function AssertNull(){

    }
    //判断文件是否存在
    protected function AssertFileExists(){

    }
    //根据正则表达式判断
    protected function AssertRegExp(){

    }*/

    //------------------------------------------------------------------------------------------------------------
    // 断言列表
    //------------------------------------------------------------------------------------------------------------

    protected function assertArrayHasKey(){

    }
    protected function assertClassHasAttribute(){

    }
    protected function assertClassHasStaticAttribute(){

    }
    protected function assertContains(){

    }
    protected function assertContainsOnly(){

    }
    protected function assertContainsOnlyInstancesOf(){

    }
    protected function assertCount(){

    }
    protected function assertEmpty(){

    }
    protected function assertEqualXMLStructure(){

    }
    protected function assertEquals(){

    }
    protected function assertFalse(){

    }
    protected function assertFileEquals(){

    }
    protected function assertFileExists(){

    }
    protected function assertGreaterThan(){

    }
    protected function assertGreaterThanOrEqual(){

    }
    protected function assertInstanceOf(){

    }
    protected function assertInternalType(){

    }
    protected function assertJsonFileEqualsJsonFile(){

    }
    protected function assertJsonStringEqualsJsonFile(){

    }
    protected function assertJsonStringEqualsJsonString(){

    }
    protected function assertLessThan(){

    }
    protected function assertLessThanOrEqual(){

    }
    protected function assertNull(){

    }
    protected function assertObjectHasAttribute(){

    }
    protected function assertRegExp(){

    }
    protected function assertStringMatchesFormat(){

    }
    protected function assertStringMatchesFormatFile(){

    }
    protected function assertSame(){

    }
    protected function assertSelectCount(){

    }
    protected function assertSelectEquals(){

    }
    protected function assertSelectRegExp(){

    }
    protected function assertStringEndsWith(){

    }
    protected function assertStringEqualsFile(){

    }
    protected function assertStringStartsWith(){

    }
    protected function assertTag(){

    }
    protected function assertThat(){

    }
    protected function assertTrue(){

    }
    protected function assertXmlFileEqualsXmlFile(){

    }
    protected function assertXmlStringEqualsXmlFile(){

    }
    protected function assertXmlStringEqualsXmlString(){

    }

    //输出
    private function output($_count, $_fail, $_success, $_time, array $_list){
        $s = '<table width="100%" border="0" cellpadding="0" cellspacing="3">
                  <tr>
                    <td>
                        <table width="600" border="0" cellspacing="2" cellpadding="0">
                          <tr>
                            <td width="100" height="30" align="center" bgcolor="#FFCC00" style="font-size: 14px">测试总数：{$_count}</td>
                            <td width="100" align="center" bgcolor="#FF0000" style="font-size: 14px">失败：{$_fail}</td>
                            <td width="100" align="center" bgcolor="#00CC00" style="font-size: 14px">通过：{$_success}</td>
                            <td width="100" align="center" bgcolor="#66CCCC" style="font-size: 14px">总耗时：{$_time}/毫秒</td>
                            </tr>
                        </table>
                    </td>
                  </tr>
                  <tr>
                    <td>
                        <table width="600" border="0" cellspacing="2" cellpadding="0">
                            <tr>
                              <td width="100" height="30" align="center" bgcolor="#BBBBBB" style="font-size: 14px"><strong>测试方法</strong></td>
                              <td width="100" align="center" bgcolor="#CDCDCD" style="font-size: 14px"><strong>结果</strong></td>
                              <td width="100" align="center" bgcolor="#E0E0E0" style="font-size: 14px"><strong>耗时</strong></td>
                            </tr>';

        foreach($_list as $v){
             $s .= '<tr>
                      <td height="30" align="center" bgcolor="#E7E7E7" style="font-size: 14px">{$v[0]}</td>
                      <td align="center" bgcolor="#EFEFEF" style="font-size: 14px">{$v[1]}</td>
                      <td align="center" bgcolor="#F5F5F5" style="font-size: 14px">{$v[2]}/毫秒</td>
                    </tr>';
        }

        $s .= '</table></td></tr></table>';
        echo($s);
    }
}
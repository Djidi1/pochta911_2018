<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="formdata">
        <div id="form_{form/@name}" class="form">

            <xsl:choose>
                <xsl:when test="@noform = 1">
                    <xsl:apply-templates select="form"/>
                </xsl:when>
                <xsl:when test="//page/@name = 'pages'">
                    <div class="row">
                        <div class="col-sm-12">
                            <form class="alert alert-info">
                                <xsl:attribute name="ENCTYPE">multipart/form-data</xsl:attribute>
                                <xsl:attribute name="name">
                                    <xsl:value-of select="form/@name"/>
                                </xsl:attribute>
                                <xsl:attribute name="action">
                                    <xsl:value-of select="form/@action"/>
                                </xsl:attribute>
                                <xsl:attribute name="method">
                                    <xsl:value-of select="form/@method"/>
                                </xsl:attribute>
                                <xsl:apply-templates select="form"/>
                            </form>
                        </div>
                    </div>
                </xsl:when>
                <xsl:otherwise>
                    <div class="row">
                        <div class="col-sm-4">

                        </div>
                        <div class="col-sm-4">
                            <form class="alert alert-info">
                                <xsl:attribute name="ENCTYPE">multipart/form-data</xsl:attribute>
                                <xsl:attribute name="name">
                                    <xsl:value-of select="form/@name"/>
                                </xsl:attribute>
                                <xsl:attribute name="action">
                                    <xsl:value-of select="form/@action"/>
                                </xsl:attribute>
                                <xsl:attribute name="method">
                                    <xsl:value-of select="form/@method"/>
                                </xsl:attribute>
                                <xsl:apply-templates select="form"/>
                            </form>
                        </div>
                    </div>
                </xsl:otherwise>
            </xsl:choose>
        </div>
    </xsl:template>
    <xsl:template match="form">
        <xsl:for-each select="item[@type = 'hidden']">
            <xsl:choose>
                <xsl:when test="@type='hidden'">
                    <xsl:call-template name="hidden"/>
                </xsl:when>
                <xsl:otherwise>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
        <table cellspacing="0" cellpadding="0" border="0">
            <xsl:for-each select="item[@type != 'hidden']">
                <xsl:choose>
                    <xsl:when test="@type='text'">
                        <xsl:call-template name="text"/>
                    </xsl:when>
                    <xsl:when test="@type='password'">
                        <xsl:call-template name="text"/>
                    </xsl:when>
                    <xsl:when test="@type='textarea'">
                        <xsl:call-template name="textarea"/>
                    </xsl:when>
                    <xsl:when test="@type='checkbox'">
                        <xsl:call-template name="checkbox"/>
                    </xsl:when>
                    <xsl:when test="@type='submit'">
                        <xsl:call-template name="submit"/>
                    </xsl:when>
                    <xsl:when test="@type='select'">
                        <xsl:call-template name="select"/>
                    </xsl:when>
                    <xsl:when test="@type='option'">
                    </xsl:when>
                    <xsl:when test="@type='file'">
                        <xsl:call-template name="file"/>
                    </xsl:when>
                    <xsl:when test="@type='filefi'">
                        <xsl:call-template name="filefi"/>
                    </xsl:when>

                    <xsl:when test="@type='message'">
                        <xsl:call-template name="message"/>
                    </xsl:when>
                    <xsl:when test="@type='lmessage'">
                        <xsl:call-template name="lmessage"/>
                    </xsl:when>
                    <xsl:otherwise>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </table>
    </xsl:template>
    <xsl:template name="text">
        <tr class="inputblock">
            <td class="caption">
                <b><xsl:value-of select="label"/></b>
            </td>
            <td class="input">
                <input>
                    <xsl:attribute name="id">
                        <xsl:value-of select="@id"/>
                    </xsl:attribute>
                    <xsl:attribute name="class"> form-control <xsl:value-of select="@className"/>
                    </xsl:attribute>
                    <xsl:attribute name="type">
                        <xsl:value-of select="@type"/>
                    </xsl:attribute>
                    <xsl:attribute name="name">
                        <xsl:value-of select="@name"/>
                    </xsl:attribute>
                    <xsl:attribute name="size">
                        <xsl:value-of select="@size"/>
                    </xsl:attribute>
                    <xsl:attribute name="value">
                        <xsl:value-of select="value"/>
                    </xsl:attribute>
                </input>
            </td>
        </tr>
    </xsl:template>
    <xsl:template name="textarea">
        <tr class="inputblock">
            <td colspan="2" class="caption">
                <xsl:value-of select="label"/>
            </td>
        </tr>
        <tr class="inputblock">
            <td colspan="2" class="input">
                <div>
                    <textarea>
                        <xsl:attribute name="cols">
                            <xsl:value-of select="@size"/>
                        </xsl:attribute>
                        <xsl:attribute name="rows">
                            <xsl:value-of select="@size div 10 +1"/>
                        </xsl:attribute>

                        <xsl:attribute name="id">
                            <xsl:value-of select="@id"/>
                        </xsl:attribute>
                        <xsl:attribute name="name">
                            <xsl:value-of select="@name"/>
                        </xsl:attribute>
                        <xsl:attribute name="class">
                            <xsl:value-of select="@className"/>
                        </xsl:attribute>
                        <xsl:attribute name="type">
                            <xsl:value-of select="@type"/>
                        </xsl:attribute>
                        <xsl:value-of select="value" disable-output-escaping="yes"/>
                    </textarea>
                </div>
            </td>
        </tr>
    </xsl:template>
    <xsl:template name="checkbox">
        <tr class="inputblock">
            <td class="caption">
                <xsl:value-of select="label"/>
            </td>
            <td class="input">
                <input>
                    <xsl:attribute name="id">
                        <xsl:value-of select="@id"/>
                    </xsl:attribute>
                    <xsl:attribute name="class">
                        <xsl:value-of select="@className"/>
                    </xsl:attribute>
                    <xsl:attribute name="type">
                        <xsl:value-of select="@type"/>
                    </xsl:attribute>
                    <xsl:attribute name="value">
                        <xsl:value-of select="value"/>
                    </xsl:attribute>
                    <xsl:attribute name="name">
                        <xsl:value-of select="@name"/>
                    </xsl:attribute>
                    <xsl:attribute name="size">
                        <xsl:value-of select="@size"/>
                    </xsl:attribute>
                    <xsl:if test="@checked = 1">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                    <xsl:if test="@postlabel != ''">
                        <xsl:value-of select="@postlabel"/>
                    </xsl:if>
                </input>
            </td>
        </tr>
    </xsl:template>
    <xsl:template name="submit">
        <tr class="inputblock">
            <td colspan="2" class="submit">
                <input>
                    <xsl:attribute name="id">
                        <xsl:value-of select="@id"/>
                    </xsl:attribute>
                    <xsl:attribute name="class">
                        <xsl:value-of select="@className"/>
                    </xsl:attribute>
                    <xsl:attribute name="type">
                        <xsl:value-of select="@type"/>
                    </xsl:attribute>
                    <xsl:attribute name="value">
                        <xsl:value-of select="value"/>
                    </xsl:attribute>
                    <xsl:attribute name="name">
                        <xsl:value-of select="@name"/>
                    </xsl:attribute>
                </input>
            </td>
        </tr>
    </xsl:template>
    <xsl:template name="select">
        <tr class="inputblock">
            <td class="caption">
                <xsl:value-of select="label"/>
            </td>
            <td>
                <select>
                    <xsl:attribute name="id">
                        <xsl:value-of select="@id"/>
                    </xsl:attribute>
                    <xsl:attribute name="class">
                        <xsl:value-of select="@className"/>
                    </xsl:attribute>
                    <xsl:attribute name="name">
                        <xsl:value-of select="@name"/>
                    </xsl:attribute>
                    <xsl:attribute name="size">
                        <xsl:value-of select="@size"/>
                    </xsl:attribute>
                    <xsl:variable name="selval" select="@selval"/>
                    <xsl:if test="$selval = 0">
                        <option>
                            <xsl:attribute name="value">0</xsl:attribute>
                            <xsl:value-of select="defaultText"/>
                        </option>
                    </xsl:if>
                    <xsl:call-template name="option">
                        <xsl:with-param name="selval">
                            <xsl:value-of select="$selval"/>
                        </xsl:with-param>
                    </xsl:call-template>
                </select>
            </td>
        </tr>
    </xsl:template>
    <xsl:template name="option">
        <xsl:param name="selval">1</xsl:param>
        <xsl:for-each select="options/option">
            <option>
                <xsl:attribute name="text">
                    <xsl:value-of select="$selval"/>
                </xsl:attribute>
                <xsl:attribute name="value">
                    <xsl:value-of select="@value"/>
                </xsl:attribute>
                <xsl:if test="$selval = @value">
                    <xsl:attribute name="selected">selected</xsl:attribute>
                </xsl:if>
                <xsl:value-of select="."/>
            </option>
        </xsl:for-each>
    </xsl:template>
    <xsl:template name="file">
        <tr class="inputblock">
            <td class="caption">
                <xsl:value-of select="label"/>
            </td>
            <td>
                <input>
                    <xsl:attribute name="id">
                        <xsl:value-of select="@id"/>
                    </xsl:attribute>
                    <xsl:attribute name="class">
                        <xsl:value-of select="@className"/>
                    </xsl:attribute>
                    <xsl:attribute name="type">
                        <xsl:value-of select="@type"/>
                    </xsl:attribute>
                    <xsl:attribute name="name">
                        <xsl:value-of select="@name"/>
                    </xsl:attribute>
                    <xsl:attribute name="size">
                        <xsl:value-of select="@size"/>
                    </xsl:attribute>
                </input>
                <xsl:if test="value != ''">
                    <span>Текущий файл:
                        <xsl:value-of select="value"/>
                    </span>
                    <span class="del">
                        <a href="/files/del-{@id}/rd-1/">удалить</a>
                    </span>
                </xsl:if>
                <input>
                    <xsl:attribute name="type">hidden</xsl:attribute>
                    <xsl:attribute name="name">fileInput</xsl:attribute>
                    <xsl:attribute name="value"></xsl:attribute>
                </input>
            </td>
        </tr>
    </xsl:template>
    <xsl:template name="filefi">
        <tr class="inputblock">
            <td class="caption">
                <xsl:value-of select="label"/>
            </td>
            <td>
                <input>
                    <xsl:attribute name="id">
                        <xsl:value-of select="@id"/>
                    </xsl:attribute>
                    <xsl:attribute name="class">
                        <xsl:value-of select="@className"/>
                    </xsl:attribute>
                    <xsl:attribute name="type">file</xsl:attribute>
                    <xsl:attribute name="name">
                        <xsl:value-of select="@name"/>
                    </xsl:attribute>
                    <xsl:attribute name="size">
                        <xsl:value-of select="@size"/>
                    </xsl:attribute>
                </input>
                <xsl:if test="value != ''">
                    <span>Текущий файл:
                        <xsl:value-of select="value"/>
                    </span>
                    <!-- <span><a href="http://{//page/@host}/files/del/"</span> -->
                </xsl:if>
                <input>
                    <xsl:attribute name="type">hidden</xsl:attribute>
                    <xsl:attribute name="name">inputName_<xsl:value-of select="@name"/>
                    </xsl:attribute>
                    <xsl:attribute name="value">
                        <xsl:value-of select="@name"/>
                    </xsl:attribute>
                </input>
            </td>
        </tr>
        <xsl:if test="isDescr = 1">
            <tr class="inputblock">
                <td>Описание:</td>
                <td>
                    <xsl:variable name="fd">
                        <xsl:text>filedescription_</xsl:text><xsl:value-of select="@name"/>
                    </xsl:variable>
                    <textarea>
                        <xsl:attribute name="cols">
                            <xsl:value-of select="@size"/>
                        </xsl:attribute>
                        <xsl:attribute name="rows">
                            <xsl:value-of select="@size div 10 +1"/>
                        </xsl:attribute>
                        <xsl:attribute name="id">
                            <xsl:value-of select="@id"/>
                        </xsl:attribute>
                        <xsl:attribute name="{$fd}">
                            <xsl:value-of select="@name"/>
                        </xsl:attribute>
                        <xsl:attribute name="class">
                            <xsl:value-of select="@className"/>
                        </xsl:attribute>
                        <xsl:attribute name="type">
                            <xsl:value-of select="@type"/>
                        </xsl:attribute>
                        <xsl:value-of select="value"/>
                    </textarea>
                </td>
            </tr>
        </xsl:if>
    </xsl:template>
    <xsl:template name="hidden">

        <input>
            <xsl:attribute name="id">
                <xsl:value-of select="@id"/>
            </xsl:attribute>
            <xsl:attribute name="type">
                <xsl:value-of select="@type"/>
            </xsl:attribute>
            <xsl:attribute name="name">
                <xsl:value-of select="@name"/>
            </xsl:attribute>
            <xsl:attribute name="value">
                <xsl:value-of select="value"/>
            </xsl:attribute>
        </input>

    </xsl:template>
    <xsl:template name="message">
        <tr>
            <td colspan="2" class="{@className}">
                <div class="alert alert-warning">
                    <span class="{@className}">
                        <xsl:value-of select="." disable-output-escaping="yes"/>
                    </span>
                </div>
            </td>
        </tr>
    </xsl:template>
    <xsl:template name="lmessage">
        <tr>
            <td class="caption">
                <span class="{@className}">
                    <xsl:value-of select="label"/>
                </span>
            </td>
            <td>
                <xsl:value-of select="value"/>
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>

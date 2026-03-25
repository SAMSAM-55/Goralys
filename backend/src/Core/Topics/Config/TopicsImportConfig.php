<?php

namespace Goralys\Core\Topics\Config;

/**
 * Configuration class for topic import processes.
 */
final class TopicsImportConfig
{
    /** @var string The default filename for the group-to-teacher mapping CSV. */
    public const string GROUPS_FILENAME = "groupes.csv";
    /** @var string The separator used to split multiple teachers in a CSV cell. */
    public const string TEACHERS_SEPARATOR = "|";
    /** @var string The separator between the topic code and name in the CSV filename. */
    public const string TOPIC_CODE_NAME_SEPARATOR = "_";
}
